var $collectionHolder;

// setup an "add an answer" link
// var $addAnswerButton = $('<button class="btn btn-primary mr-2" type="button"><i class="fas fa-plus"></i> Add an answer</button>');
var $addAnswerButton = $("#answers-add");
var $newLinkLi = $('<div id="answers"></div>').append($addAnswerButton);

jQuery(document).ready(function() {

    // Get the ul that holds the collection of answers
    $collectionHolder = $('#question_answers');

    // add a delete link to all of the existing tag form li elements
    $collectionHolder.find('fieldset').each(function() {
        addAnswerFormDeleteLink($(this));
    });

    // add the "add an answer" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find('fieldset').length);

    $addAnswerButton.on('click', function(e) {
        // add a new answer form (see next code block)
        addAnswerForm($collectionHolder, $newLinkLi);
    });
    var index = $collectionHolder.data('index');
});

function addAnswerForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you set 'label' => false in your tags field in TaskType Replace '__name__label__' in the prototype's HTML to instead be a number based on how many items we have newForm = newForm.replace(/__name__label__/g, index);
    // Replace '__name__' in the prototype's HTML to instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<div></div>').append(newForm);
    //var $newFormLi = $(newForm);
    $newLinkLi.before($newFormLi);

    // add a delete link to the new form
    addAnswerFormDeleteLink($newFormLi);
}

function addAnswerFormDeleteLink($answerFormLi) {
    var $removeFormButton = $('<button class="btn btn-danger btn-sm mr-2 align-self-end" type="button"><i class="fas fa-trash-alt"></i></button>');    
    $answerFormLi.addClass('card bg-light border-dark p-3 mb-3');    
    $answerFormLi.append($removeFormButton);
    // $removeFormButton.css("float", "right");

    $removeFormButton.on('click', function(e) {
        // remove the li for the answer form
        $answerFormLi.remove();
    });
}
