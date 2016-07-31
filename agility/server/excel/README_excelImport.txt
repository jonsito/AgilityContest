Dog importing from Excel .xlsx files into AgilityContest database
=================================================================

- You can use "Excel export" to generate import templates

- Header may match english or AgilityContest pre-defined language
 ( ie: if spanish is choosen as language, you can use "Nombre" "Categoría" "Grado", etc )

- Required headers for dog import
* Name - Dog name
* Category - Standard, medium, small, toy, and so. Abreviatures are allowed
* Grade - A1/G1/GII and similar expressions are allowed. if not used, set to "-"
* Handler - Dog Handler name
* Club - Handler's Club name 
	On international contests fill this column with Country

- Optional headers for dog import

* LongName - Pedigree dog name
* Breed - dog breed
* Gender - Male/Female/Dog/Bitch and locale variants

WHAT DOES BLIND MODE MEANS:

Blind Mode means that user assumes existing data in AgilityContest Database matches with their counterpart in Excel file, or not exists. No check are performed, and no user action is requested. Searches are done by exact match, no similar names
- Club/Country MUST exist in database and match name. Else error will be shown
- If a handler with matching name and club is found in database, will be used. Otherwise a new handler will be created
- If a dog with matching name and handler is found in database, will be used. Otherwise, a new dog will be created
