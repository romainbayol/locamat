describe('Test de connexion', () => {
  it('Test Connexion, remplir formulaire materiel, creation de compte puis suppression de celui ci', () => {
    cy.visit('http://77.202.74.30/') // Ici on ouvre la page d'accueil

    cy.get('.btn-login').click() // On clique sur se connecter pour aller sur la page de connexion

    cy.url().should('include', 'login.php') // On vérifie que l'on est sur la page de connexion

    cy.get('#floatingInput').type('romain.bayol@icloud.com') // On remplit l'adresse email
    cy.get('#floatingPassword').type('test') // le mot de passe

    cy.get('.w-100.btn.btn-lg.btn-primary').click() // On se connecte

    cy.url().should('eq', 'http://77.202.74.30/index.php') // On vérifie qu'on est redirigé vers la page d'accueil

    // 2️Aller à la page "Matériels"
    cy.get('a[href="materiels.php"]').click()
    cy.url().should('include', 'materiels.php')

    // Cliquer sur "Ajouter un matériel"
    cy.get('a[href="ajouter_materiel.php"]').click()
    cy.url().should('include', 'ajouter_materiel.php')

    // Remplir le formulaire avec les données spécifiées
    cy.get('input[name="nom"]').type('TestAuto') // Nom du matériel
    cy.get('input[name="version"]').type('V12') // Version
    cy.get('input[name="ref"]').type('AUTO12') // Référence

    // Sélectionner "PC" dans la liste déroulante
    cy.get('select[name="categorie"]').select('PC')

    // Ajouter une description
    cy.get('textarea[name="description"]').type('Test description en auto')

    // Vérifier que l’image par défaut est bien présente
    cy.get('#current-image').should('have.attr', 'src', 'img/default_image.png')

    // Cliquer sur le bouton "Ajouter"
    cy.get('button[type="submit"]').contains('Ajouter').click()

    // Retourner à la page "Matériels"
    cy.get('a[href="materiels.php"].btn-secondary').click();
    cy.url().should("include", "materiels.php");

    // Aller à la page "Comptes"
    cy.get('a[href="comptes.php"]').click();
    cy.url().should("include", "comptes.php");

    // Ajouter un compte
    cy.get('a[href="ajouter_comptes.php"]').click();
    cy.url().should("include", "ajouter_comptes.php");

    // Remplir le formulaire d'ajout de compte
    cy.get('input[name="nom"]').type("testauto"); // Nom
    cy.get('input[name="prenom"]').type("testauto"); // Prénom
    cy.get('input[name="email"]').type("testauto@gmail.com"); // Email
    cy.get('input[name="matricule"]').type("1212121212"); // Matricule
    cy.get('input[name="password"]').type("testauto"); // Mot de passe

    // Sélectionner "Utilisateur" (valeur 0)
    cy.get('select[name="role"]').select("0");

    // Valider l'ajout du compte
    cy.get('button[type="submit"]').contains("Ajouter").click();

    // Attendre que la page "comptes.php" se charge bien
    cy.wait(2000);
    cy.url().should("include", "comptes.php");

    // Vérifier que le compte apparaît bien avant de tenter la suppression
    cy.contains("td", "testauto", { timeout: 6000 }).should("be.visible");

    // Suppression du compte "testauto"
    cy.contains("td", "testauto") // Trouve la ligne avec le nom "testauto"
      .parent() // Remonte au <tr> associé
      .find('.btn-danger') // Trouve le bouton "Supprimer"
      .click();

    // Confirmer la suppression (si une boîte de dialogue apparaît)
    cy.on("window:confirm", (str) => {
      expect(str).to.equal("Êtes-vous sûr de vouloir supprimer ce compte ?");
      return true; // Accepte la suppression
    });

    // Vérifier que le compte a bien été supprimé
    cy.contains("testauto").should("not.exist");
  })
})



    