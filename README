
# Classifier is a simple self learning PHP library for detecting Spam messages.

Classifier can be used to classify wether comments are ham or spam.

* Fast
* Easy to extend with your own implementation.
* No configuration needed.

## Usage

### Learn or train documents:

	$text = 'Hello, World!';

	$Document = new ClassifierDocumentImpl();
	$Document->setText($text);

	$Classifier = new ClassifierImpl();
	$Classifier->learn($Document, $totalHamCount, $totalSpamCount, ClassifierImpl::HAM);

### Check wether a document is ham or spam

	$text = 'What is the meaning of this?';

	$Document = new ClassifierDocumentImpl();
	$Document->setText($text);

	$Classifier = new ClassifierImpl();
	$Classifier->check($Document);

	if ($Classifier->isHam() {
		echo "HAM";
	} else {
		echo "SPAM";
	}
