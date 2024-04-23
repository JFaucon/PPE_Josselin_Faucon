describe('Login Client', () => {
  beforeEach(() => {
    cy.visit('/login')
  })

  it('can login as client', () => {
    cy.get('#username').type('client@client.com')
    cy.get('#password').type('client')
    cy.get('[data-cy="login"]').click()
    cy.location('pathname').should('eq', '/');
  })
})