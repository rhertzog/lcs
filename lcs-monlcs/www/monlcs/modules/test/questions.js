// Pour construire de nouveaux objets
function question(answer, support, question, a, b, c, d) {
	this.answer = answer;
    this.support = support;
	this.question = question;
	this.a = a;
	this.b = b; 
	this.c = c;
	this.d = d;
	return this;
	}

// Le tableau des questions, r�ponses, options et explications
var units = new Array(
	new question("a","","Quelle est la couleur du cheval blanc de Napol�on","Blanc","Gris","Bleu","Noir"),
new question("a","","Qui est mrT ?","Barracuda","Capitaine crochet","Zorro","Alice")

	);
