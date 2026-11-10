/** Placeholders: change these in one place. */
export const SITE_URL = 'https://example.com';
export const GITHUB_URL = 'https://github.com/archboard/laravel-powerschool-starter';

/** schema.org nodes shared by every docs page. */
export const ARCHBOARD = {
  '@type': 'Organization',
  '@id': 'https://archboard.io/#organization',
  name: 'Archboard',
  url: 'https://archboard.io',
  sameAs: ['https://github.com/archboard'],
};
export const WEBSITE = {
  '@type': 'WebSite',
  name: 'PowerSchool Starter',
  alternateName: 'PowerSchool Starter',
  url: `${SITE_URL}/`,
  publisher: ARCHBOARD,
};

