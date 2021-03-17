context('Tenant Settings', () => {
  beforeEach(() => {
    cy.login()
  })
  afterEach(() => {
    cy.logout()
  })

  it('Cannot update tenant settings without permission', () => {
    cy.request({
      failOnStatusCode: false,
      url: '/settings/tenant',
    }).then(res => {
      expect(res.status).to.eq(403);
    })
  })

  it('Updates tenant info successfully', () => {
    cy.addPermissions(['edit tenant settings'])

    const data = {
      name: 'new tenant name',
      ps_url: 'https://example.com',
      ps_client_id: '03f7db62-e1a7-497d-8d04-d892ba3145ba',
      ps_secret: 'ed700686-b5c5-4064-83fb-eec81144f1cf',
    }

    cy.visit('/settings/tenant')

    // Update the form values
    Object.keys(data).forEach(name => {
      cy.getCy(name).clear().type(data[name])
    })
    cy.getCy('allow_password_auth').click()
    cy.getCy('form').submit()

    // Assert that there was a message
    cy.getPage().should('contain.text', 'Settings updated successfully.')

    // Visit the page again to verify the data matches
    cy.visit('/settings/tenant')
    Object.keys(data).forEach(name => {
      cy.getCy(name).invoke('val').should('eq', data[name])
    })
    cy.getCy('allow_password_auth').should('be.checked')
  })

  it('Fails validation', () => {
    cy.addPermissions(['edit tenant settings'])

    const data = {
      name: 'new tenant name',
      ps_url: 'https://',
      ps_client_id: 'invalid format',
      ps_secret: 'invalid format',
    }

    cy.visit('/settings/tenant')

    // Update the form values
    Object.keys(data).forEach(name => {
      cy.getCy(name).clear().type(data[name])
    })
    cy.getCy('allow_password_auth').click()
    cy.getCy('form').submit()

    // Assert that are error messages
    cy.getPage().should('not.contain.text', 'Settings updated successfully.')
    cy.getCy('form').should('contain.text', 'format is invalid.')
    cy.getCy('form').should('contain.text', 'must be a valid UUID.')
  })
})
