function getconfirminitpass() {
	if(confirm("Confirmez la reinitialisation du mot de passe ?")) {
		return true;
	} else {
		return false;
	}
}

function getconfirm() {
	if(confirm("Confirmez la suppression ?")) {
		return true;
	} else {
		return false;
	}
}