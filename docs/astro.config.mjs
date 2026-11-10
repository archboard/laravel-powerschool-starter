// @ts-check
import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';
import tailwindcss from '@tailwindcss/vite';
import starlightLlmsTxt from 'starlight-llms-txt';
import starlightLinksValidator from 'starlight-links-validator';

import { SITE_URL, GITHUB_URL } from './src/config.ts';

export default defineConfig({
  site: SITE_URL,
  trailingSlash: 'always',
  integrations: [
    starlight({
      title: 'PowerSchool Starter',
      description: 'Documentation for PowerSchool Starter, a multi-tenant PowerSchool app starter with SSO and sync.',
      logo: { light: './src/assets/archboard-light.svg', dark: './src/assets/archboard-dark.svg', alt: 'Archboard' },
      favicon: '/favicon.png',
      social: [{ icon: 'github', label: 'GitHub', href: GITHUB_URL }],
      editLink: { baseUrl: `${GITHUB_URL}/edit/main/docs/` },
      lastUpdated: true,
      customCss: ['./src/styles/global.css'],
      components: {
        Head: './src/components/Head.astro',
        Footer: './src/components/Footer.astro',
      },
      head: [
        { tag: 'meta', attrs: { name: 'author', content: 'Archboard' } },
        { tag: 'meta', attrs: { name: 'twitter:card', content: 'summary_large_image' } },
      ],
      plugins: [
        starlightLinksValidator(),
        starlightLlmsTxt({
          projectName: 'PowerSchool Starter',
          description: 'PowerSchool Starter is a multi-tenant Laravel starting point by Archboard for apps that integrate with PowerSchool (SSO and data sync).',
          details: '- It can be self-hosted (Docker or a VM) or run as a managed cloud.\n- Each district is a tenant; a tenant has schools, and users act within a current school.\n- User types are staff, guardian (contact) and student. Staff abilities come from per-school permissions.',
          promote: ['getting-started/**', 'installation/**'],
          // One copy of each screenshot in llms-full.txt, none in llms-small.txt
          customSelectors: { all: ['img.dark-only'], small: ['figure.screenshot'] },
        }),
      ],
      sidebar: [
        { label: 'Getting started', items: [{ autogenerate: { directory: 'getting-started' } }] },
        { label: 'Installation', items: [{ autogenerate: { directory: 'installation' } }] },
        { label: 'Integrations', items: [{ autogenerate: { directory: 'integrations' } }] },
        { label: 'Reference', collapsed: true, items: [{ autogenerate: { directory: 'reference' } }] },
        'contributing',
      ],
    }),
  ],
  vite: { plugins: [tailwindcss()] },
});
