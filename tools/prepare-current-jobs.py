"""Read the public legacy site and prepare an auditable import snapshot."""
import datetime
import html
import json
from pathlib import Path
import re
import urllib.request

url = 'https://www.foods-selection.co.jp/job/index.php?rest_route=/wp/v2/posts&per_page=100&_fields=id,title,link,content'
with urllib.request.urlopen(url, timeout=40) as response:
    posts = json.load(response)

def text(value):
    value = re.sub(r'<(?:script|style)\b[^>]*>.*?</(?:script|style)>', '', value, flags=re.S | re.I)
    value = re.sub(r'<br\s*/?>|</(?:p|div|tr|td|th|li|h[1-6])>', '\n', value, flags=re.I)
    value = html.unescape(re.sub(r'<[^>]*>', '', value)).replace('\xa0', ' ')
    return '\n'.join(line.strip() for line in value.splitlines() if line.strip())

jobs = []
for post in posts:
    title = text(post['title']['rendered'])
    if not re.match(r'^＜.+＞', title):
        continue
    content = text(post['content']['rendered'])
    labels = re.compile(r'(?m)^(募集職種|職種別|待遇|勤務地|お問合せ)$')
    matches = list(labels.finditer(content))
    sections = {match[1]: content[match.end():matches[i+1].start() if i+1 < len(matches) else len(content)].strip()
                for i, match in enumerate(matches)}
    assert all(key in sections for key in ['募集職種', '職種別', '勤務地', 'お問合せ']), post['id']
    conditions = re.sub(r'^(勤務時間・時給|時給)\s*', '', sections['職種別'])
    shop = re.match(r'^＜(.+?)＞', title)[1]
    if shop == 'しいの木台':
        shop += '店'
    jobs.append({'source_id': post['id'], 'source_url': post['link'], 'title': title,
                 'shop_name': shop, 'occupation': sections['募集職種'].splitlines()[0],
                 'occupation_description': sections['募集職種'], 'conditions': conditions,
                 'benefits': sections.get('待遇', ''), 'work_location_access': sections['勤務地'],
                 'application_method': sections['お問合せ'], 'source_text': content,
                 'source_html': post['content']['rendered']})
assert len(jobs) == 34, f'Unexpected source count: {len(jobs)}'
payload = {'source': url, 'fetched_at': datetime.datetime.now(datetime.timezone.utc).isoformat(), 'jobs': jobs}
out = Path(__file__).with_name('current-jobs-snapshot.json')
out.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
check = out.read_text(encoding='utf-8', errors='strict')
assert '\ufffd' not in check and '\x00' not in check
for job in jobs:
    print(f"{job['source_id']}: {job['shop_name']} / {job['occupation']}")
print(f'Prepared {len(jobs)} jobs: {out}')
