import '../../node_modules/cypress-xpath'

Cypress.on('uncaught:exception', (err, runnable) => {  //Esto es para silenciar los errores propios de la página que están fuera de mi control.
  return false;
});

