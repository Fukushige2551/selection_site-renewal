import '../scss/page-recruit-entry.scss';
import './form-privacy-feedback.js';
import './application-validation.js';
import './form-kana-date-validation.js';
import { setupSubmitValidation } from './form-submit-validation.js';

const form = document.querySelector('.p-recruit-entry__form');

setupSubmitValidation(form);
