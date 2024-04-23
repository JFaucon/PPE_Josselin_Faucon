describe('profile redirection', () => {
  it('passes', () => {
    cy.visit('/profile')
    cy.location('pathname').should('eq', '/login');
  })
})