describe('Test connexion puis modification samsung', () => {
  it('Test connexion puis modification samsung', () => {
    cy.visit('http://77.202.74.30/') // Ici on ouvre la page d'accueil

    cy.get('.btn-login').click() // On clique sur se connecter pour aller sur la page de connexion

    cy.url().should('include', 'login.php') // On vérifie que l'on est sur la page de connexion

    cy.get('#floatingInput').type('romain.bayol@icloud.com') // On remplit l'adresse email
    cy.get('#floatingPassword').type('test') // le mot de passe

    cy.get('.w-100.btn.btn-lg.btn-primary').click() // On se connecte

    cy.url().should('eq', 'http://77.202.74.30/index.php') // On vérifie qu'on est redirigé vers la page d'accueil

    // Aller à la page "Matériels"
    cy.get('a[href="materiels.php"]').click()
    cy.url().should('include', 'materiels.php')

    // Vérifier que le matériel "Samsung" existe avant de le modifier
    cy.contains("td", "Samsung", { timeout: 6000 }).should("be.visible");

    // Cliquer sur "Modifier" pour le matériel avec ID = 1
    cy.get('a[href="modifier_materiel.php?id=1"].btn-secondary.btn-sm').click();
 
    // Vérifier qu'on est bien sur la page de modification
    cy.url().should("include", "modifier_materiel.php?id=1");
 
    // Ajout d'une vérification pour s'assurer que le formulaire de modification s'affiche
    cy.get('input[name="nom"]').should("have.value", "Samsung"); // Vérifie que le champ Nom contient "Samsung"

    // Vérifier que le champ "Version" contient bien "V8.8"
    cy.get('input[name="version"]').should("have.value", "V8.8");

    // Ecrire la version de "V8.8" à "V9"
    cy.get('input[name="version"]').clear().type("V9");
    
    // Enregistrer les modifications
    cy.get('button[type="submit"]').contains("Enregistrer").click();
    
    // Retour à la page "Matériels" pour vérifier la modification
    cy.get('a[href="materiels.php"]').click();
    cy.url().should("include", "materiels.php");
    
    // Retour sur la page de modification du matériel "Samsung"
    cy.get('a[href="modifier_materiel.php?id=1"].btn-secondary.btn-sm').click();
    cy.url().should("include", "modifier_materiel.php?id=1");
    
    // Vérifier que la version est à "V9"
    cy.get('input[name="version"]').should("have.value", "V9");
    
    // Ecrire V8.8
    cy.get('input[name="version"]').clear().type("V8.8");
    
    // Enregistrer
    cy.get('button[type="submit"]').contains("Enregistrer").click();
    
    // Retour à la page "Matériels" pour confirmer la modification
    cy.get('a[href="materiels.php"]').click();
    cy.url().should("include", "materiels.php");
    
    // Vérifier que la version est bien revenue à "V8.8"
    cy.get('a[href="modifier_materiel.php?id=1"].btn-secondary.btn-sm').click();
    cy.get('input[name="version"]').should("have.value", "V8.8");

    // Appui sur le bouton "retour"
    cy.get('a[href="materiels.php"].btn-secondary').click();
    cy.url().should("include", "materiels.php");
  })
})