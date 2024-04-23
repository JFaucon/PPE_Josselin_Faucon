describe('Reservation', () => {
  it('should allow a user to make a reservation', () => {
    cy.login('client@client.com', 'client')
    // Visitez la page de réservation
    cy.visit('/reservation')

    // Remplissez les champs nécessaires pour la réservation
    cy.get('#reservation_forfait').select('13')
    cy.get('#reservation_quantity').clear().type('2')

    // Soumettez le formulaire de réservation
    cy.get('form').submit()

    // Vérifiez que la réservation a été effectuée avec succès
    cy.url().should('eq', 'http://localhost:8000/');
  })
})