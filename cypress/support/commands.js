// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
Cypress.Commands.add('login', () => cy.request('/_testing/session/new'))
Cypress.Commands.add('logout', () => cy.request('/_testing/session/logout'))
Cypress.Commands.add('removeUser', () => cy.request('/_testing/session/logout'))
Cypress.Commands.add('getCy', (name, ...args) => cy.get(`[data-cy="${name}"]`, ...args))
Cypress.Commands.add('getPage', (...args) => cy.get(`[data-cy="page"]`, ...args))
Cypress.Commands.add('addPermissions', (permissions, model) => {
  cy.request('post', '/_testing/permissions', {
    permissions,
  })
})

//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
