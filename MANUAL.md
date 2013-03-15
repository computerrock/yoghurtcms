
Field Types
-----------

### 1. Short text

This type of field holds string values with a maximum of 255 characters. It’s 
rendered as HTML text input field.


### 2. Long text

This type of field holds strings of indefinite value. By default, it’s rendered 
as HTML textarea, but can be set to show a rich editor.


**Options**   
*ckeditor:* [any value]  
If this option is set, the field will be rendered as [CKEditor](http://ckeditor.com/)   


### 3. Numeric

This field can hold numeric values with precision of 30 and scale of 10.


**Options**  
*precision:* integer  
This specifies how many decimals will be allowed until the field rounds the 
submitted value


*rounding_mode:* integer  
If a submitted number needs to be rounded (based on the precision option), you
have several configurable **Options**   for that rounding. The given value must
be a constant of IntegerToLocalizedStringTransformer.


*grouping:* (boolean)  
If you set this to true, numbers will be grouped with a comma or period
(depending on your locale): 12345.123 would display as 12,345.123.


### 4. Date / time

Stores a datetime value in the database. It will be rendered as an ordinary HTML
text field, but on click it will popup a jQuery UI calendar with hours and
minutes selectors. The format is year-month-day hour:minute


### 5. File

Allows a file to be uploaded. Renders a standard HTML file input. 

Files will be stored in the directory specified in config files under 
*spoiled_milk_yoghurt.yoghurt_service.upload_dir*. Root for upload_dir is 
Symfony’s web directory. 

**Note** that original file names and extensions will not be preserved. To avoid 
naming conflicts, Yoghurt will generate the new file name using the following 
pattern:

    { php uniquid() }.{ guessed extension based on the file’s mime type}

If extension can’t be guessed, it will be set to "bin".


### 6. Choice

Presents choices to the user. Renders an HTML select element, radio buttons or 
checkboxes, depending on "multiple" and "expanded"


**Options**  
*choices:* array  
example:
```json
{"m": "Male", "f": "Female", "u":"Unspecified"}
```
A json object, where the variable name is the item value and the variable’s 
value is the item's label.


*multiple:* boolean  
default: false


*expanded:* boolean  
default: false

What gets rendered, depending on *multiple* and *expanded* settings:  
<table>
    <tr>
        <th>element type</th>
        <th>expanded</th>
        <th>multiple</th>
    </tr>
    <tr>
        <td>select tag</td>
        <td>false</td>
        <td>false</td>
    </tr>
    <tr>
        <td>select tag (with "multiple" attribute)</td>
        <td>false</td>
        <td>true</td>
    </tr>
    <tr>
        <td>radio buttons</td>
        <td>true</td>
        <td>false</td>
    </tr>
    <tr>
        <td>checkboxes</td>
        <td>true</td>
        <td>true</td>
    </tr>
</table>


*empty_value:* string  
default: "Please select an option"  
This option determines whether or not a special "empty" option (e.g. "Choose an 
option") will appear at the top of a select widget. This option only applies if 
both the expanded and multiple **Options**   are set to false.


*empty_data:* mixed  
default: array() if multiple or expanded are set to true, else '' (empty string)  
This option determines what value the field will return when the empty_value 
choice is selected.


*preffered_choices:* array  
default: array()  
If this option is specified, then a sub-set of all of the options will be moved 
to the top of the select menu. This is only meaningful when rendering as a 
select element (expanded is set to false). The preferred choices and normal 
choices are separated visually by a set of dotted lines (-------------------).


### 7. Relationship

This field will render a choice for the user, with other entities from the CMS 
given as options to choose from.


**Options**  
*type:* string  
Name of the entity type whose entities will be listed as options.  
Example: If the user wants entities of "Image Page" type to be listed in the 
choice, this field should have "Image Page" value.

Options inherited from Choice type: empty_value, empty_data, multiple, expanded


### 8. Google map

This field type carries geographical coordinates (longitude and latitude). It 
will present a map to the user on which he can point a single or multiple locations.


**Options**  
*multi:* boolean  
default: false  
If multi is set to true, the map will allow multiple points to be selected.


Field Constraints
-----------------

All constraints are using Symfony’s built in constraints. The documentation is 
available here: http://symfony.com/doc/2.0/reference/constraints.html


**1. not_blank**  
value: 1  
The field’s value must not be empty (either blank string or null)


**2. true**  
value: 1  
The field’s value must evaluate to PHP "true" value


**3. false**  
value: 1  
The field’s value must evaluate to PHP "false" value


**4. email**  
value: 1 or "checkMX"  
The field’s value must be an email address. If value is "checkMX", the CMS will 
also check if the domain is registered as an email server.


**5. min_length**  
value: [integer]  
The field value must have the given number of characters. Note that blank or 
null fields will validate as correct.


**6. max_length**  
value: [integer]  
The field value must not have more than the given number of characters. Note 
that blank or null fields will validate as correct.


**7. url**  
value: json array (of strings)  
example: 
```json
[ "http", "https" ]
```
The field value must be recognized as an URL, and it’s protocol must be specified 
in the array.


**8. regex**  
value: json object  
```
{
"pattern": (string value),
"match": (boolean),
"message": (string value)
}
```
The field is validated against the given regular expression in pattern field. 
If match is set to *true*, then the value must match the regex, if it’s set to 
*false*, then the value must not match the given regex. Value given in the 
message is shown to the user if validation fails.

**Note** that PHP must be able to evaluate the given JSON object, so it’s 
recommended that you try the object first, as regex can often contains characters 
that need to be escaped.


**9. max**  
value: numeric  
In order to pass validation, the field’s value must be numeric, and must be 
equal or less than the number set in the constraint.


**10. min**  
value: numeric  
The value entered in the field must be numeric, and must be equal or greater 
than the number set in the constraint.


**11. file**  
value: json object
```
{
"maxSize": mixed (integer or string)
"mimeTypes": mixed (array or string)
"maxSizeMessage": string
"mimeTypesMessage": string
"notFoundMessage": string
"notReadableMessage": string
"uploadIniSizeErrorMessage": string
"uploadFormSizeErrorMessage": string
"uploadErrorMessage": string
}
```

The file being uploaded to the CMS must satisfy the given constraints.  

maxSize (optional): If a numeric value is given, it will be treated as bytes. 
If the value is appended with "k" or "M", the value will be treated as kilobytes
 or megabytes.

mimeTypes (optional): The uploaded file’s mime type must match the specified one 
(or one of the specified values, if an array is given). Value or values should 
be in accordance with IANA specified MIME types: http://www.iana.org/assignments/media-types


**Note** that both maxSize and mimeTypes are optional, but at least one of those should be specified for the constraint to make any sense.


All message values are optional. If a value is not specified, the default Symfony message will be shown to the user.


**12. image**  
The Image constraint works exactly like the File constraint, except that its
*mimeTypes* and *mimeTypesMessage* are automatically set to work for image files.