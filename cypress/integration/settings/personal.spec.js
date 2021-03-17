context('Personal Settings', () => {
  beforeEach(() => {
    cy.login()
  })
  afterEach(() => {
    cy.logout()
  })

  it('Updates personal settings successfully', () => {
    cy.intercept({
      url: '/settings/personal',
      method: 'post',
    }).as('settings')
    cy.visit('/settings/personal')

    const data = {
      first_name: 'Jimmy',
      last_name: 'Johnson',
      email: 'jimmy@nascar.com',
      password: '',
      password_confirmation: '',
    }

    // The values should be there by default
    cy.getCy('first_name').invoke('val').should('not.be.empty')
    cy.getCy('last_name').invoke('val').should('not.be.empty')
    cy.getCy('email').invoke('val').should('not.be.empty')
    cy.getCy('password').invoke('val').should('be.empty')
    cy.getCy('password_confirmation').invoke('val').should('be.empty')

    // Set the values
    cy.getCy('first_name').clear().type(data.first_name)
    cy.getCy('last_name').clear().type(data.last_name)
    cy.getCy('email').clear().type(data.email)
    cy.getCy('password').clear().type('data.email')
    cy.getCy('password_confirmation').clear().type('data.email')
    cy.getCy('form').submit()

    cy.wait('@settings')
    cy.getPage().should('contain.text', 'Settings updated successfully.')

    // Make sure the values have been saved
    cy.visit('/settings/personal')
    cy.getCy('first_name').invoke('val').should('eq', data.first_name)
    cy.getCy('last_name').invoke('val').should('eq', data.last_name)
    cy.getCy('email').invoke('val').should('eq', data.email)
    cy.getCy('password').invoke('val').should('be.empty')
    cy.getCy('password_confirmation').invoke('val').should('be.empty')

  })

  it('Receives validation errors', () => {
    cy.visit('/settings/personal')

    // Set the values
    cy.getCy('first_name').clear()
    cy.getCy('last_name').clear()
    cy.getCy('email').clear()
    cy.getCy('password').clear().type('data.email')
    cy.getCy('password_confirmation').clear().type('data')
    cy.getCy('form').submit()

    // Make sure the values have been saved
    cy.getCy('form').should('contain.text', 'This field is required')
    cy.getCy('form').should('contain.text', 'The password confirmation does not match')
    cy.getPage().should('not.contain.text', 'Settings updated successfully.')
  })
})
