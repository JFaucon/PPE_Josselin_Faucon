describe('Login Client', () => {
  beforeEach(() => {
    cy.visit('http://localhost:8000/login')
  })

  it('can login as client', () => {
    cy.get('#username').type('client@client.com')
    cy.get('#password').type('client')
    cy.get('[data-cy="login"]').click()
    cy.url().should('eq', 'http://localhost:8000/');
  })
})