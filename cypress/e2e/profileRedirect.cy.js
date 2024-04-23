describe('profile redirection', () => {
  it('passes', () => {
    cy.visit('http://localhost:8000/profile')
    cy.url().should('eq', 'http://localhost:8000/login');
  })
})