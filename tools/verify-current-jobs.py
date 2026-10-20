"""Read-only verification of all imported public detail and entry pages."""
import concurrent.futures
import html
import json
from pathlib import Path
import re
import urllib.parse
import urllib.request

ROOT = 'http://foods-selectioncojp.local'
def get(url):
    url = urllib.parse.quote(url, safe=':/?&=%')
    return urllib.request.urlopen(url, timeout=45).read().decode('utf-8')
def text(value):
    return re.sub(r'\s+', '', html.unescape(re.sub(r'<[^>]*>', '', value)))
source = json.loads(Path(__file__).with_name('current-jobs-snapshot.json').read_text(encoding='utf-8'))['jobs']
local = json.loads(get(ROOT + '/wp-json/wp/v2/recruit_part_time?per_page=100'))
assert len(local) == 34, len(local)
by_title = {html.unescape(p['title']['rendered']): p for p in local}
assert len(by_title) == 34

def verify(job):
    post = by_title[job['title']]
    page = get(ROOT + '/job/detail/' + str(post['id']))
    requirements = re.search(r'<section class="p-job-detail__requirements".*?</section>', page, re.S)[0]
    for key in ['occupation_description', 'conditions', 'benefits', 'work_location_access', 'application_method']:
        assert text(job[key]) in text(requirements), (post['id'], key)
    assert '採用担当 ○○' not in requirements
    assert '000-0000-0000' not in requirements
    assert '&lt;br' not in requirements
    if not job['benefits']:
        assert not re.search(r'<dt>\s*待遇\s*</dt>', requirements)
        assert '高校生歓迎' not in requirements
    entry = html.unescape(re.search(r'class="p-job-detail__entry-button" href="([^"]+)"', page)[1])
    query = urllib.parse.parse_qs(urllib.parse.urlparse(entry).query)
    assert query['store'] == [job['shop_name']]
    assert query['job'] == [job['occupation']]
    form = get(entry)
    for name, expected in [('store', job['shop_name']), ('job', job['occupation'])]:
        select = re.search(r'<select[^>]*name="' + name + r'".*?</select>', form, re.S)[0]
        selected = re.search(r'<option value="([^"]*)" selected="selected">(.*?)</option>', select, re.S)
        assert selected and html.unescape(selected[1]) == expected and html.unescape(selected[2]) == expected, (post['id'], name)
    breadcrumb = re.search(r'<nav class="p-business-form-page__breadcrumb".*?</nav>', form, re.S)[0]
    assert text(job['title']) in text(breadcrumb)
    return post['id']

with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool:
    for post_id in pool.map(verify, source):
        print(f'PASS {post_id}: original sections, selected shop/occupation, breadcrumb', flush=True)
print('PASS: all 34 published jobs')
