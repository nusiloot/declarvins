function(doc) {
    if (doc.type != "CompteTiers") {
        return;
    }
    var numero_contrat = doc.contrat.replace("CONTRAT-", "");
    emit([doc.statut, doc.contrat_valide, numero_contrat, doc.nom, doc.prenom, doc.login, doc.email, doc.raison_sociale], null);

}