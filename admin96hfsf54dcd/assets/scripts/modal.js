// Code to execute when page is loaded
$(document).ready(function() {
    const choiseText = $('input[name]').filter(function() {
        return this.name.match(/^choiseText\[\d+\]$/);
    });
    // Get the button element
    const button = $('.showModal');

    // Get the modal element
    const modal = $('.modal');

    const modalBody = $('.modal_body');

    button.on('click', function() {
        let form = $('.'+ $(this).data('formName'));
        if($(this).hasClass('updateQuestion')) {
            if(confirm('Modifier les questions rendra obsolètes les résultats du formulaire. Pour supprimer une réponse dans les questions a choix, supprimez la question avec et remplissez les réponses que vous garderez. Êtes-vous sûr de le vouloir ?') == false) {
                return;
            }

            if(form.find('input[name="action"]').val() === 'add'){
                form.find('input[name="action"]').val('update');
                form.find('input[name="submit"]').val('Modifier');
                form.find('label[for="image[0]"]').text(form.find('label[for="image[0]"]').text().replace('Ajouter','Modifier'));
            }
            getQuestion($(this).data('id'),form,choiseText);
        }
        if($(this).hasClass('showModalQuestion')) {
            if(form.find('input[name="action"]').val() === 'update'){
                form.find('input[name="action"]').val('add');
                form.find('input[name="submit"]').val('Ajouter');
                form.find('label[for="image[0]"]').text(form.find('label[for="image[0]"]').text().replace('Modifier','Ajouter'));
            }

            fillForm(JSON.parse('{"REPONSES":[]}'),form,choiseText);
        }
        // définir a display flex
        form.css('display', 'flex');
    });

    // Event listener for clicking outside the modal
    $(window).on('click', function(event) {
        if (!$(event.target).is(modalBody) && !$(event.target).is(button) && !$(event.target).closest(modalBody).length && modal.is(':visible')){
            modal.hide();
        }
    });
});

function getQuestion(id, modalForm,choiseText) {
    $.ajax({
        url: adminRoot + 'question/?action=getQuestion&id=' + id,
        method: 'POST',
        data: {id: id},
        success: function(response) {
            fillForm(JSON.parse(response), modalForm,choiseText);
        },
        error: function(error) {
            alert(error);
        }
    });
}

function fillForm(data, form,choiseText ) {
    displayInputsByType(form,data.TYPE);
    // Question

    form.find('input[name="id"]').val(data.ID);
    form.find('textarea[name="libelle"]').val(data.LIBELLE);
    form.find('textarea[name="imageDescription[0]"]').val(data.IMG_DESC);
    form.find('input[name="imageDescription[1]"]').val(data.IMG_SECOND_DESC);

    // Réponses
    form.find('input[name="questionType"][value="'+data.TYPE+'"]').prop('checked', true);
    form.find('input[name="slider"]').val(data.SLIDER);
    if (data.REPONSES.length == 0) {
        form.find('input[name="choiseText[0]"]').val('');
        form.find('input[name="imageRespDesc[0]"]').val('');
        form.find('input[name="choiseText[1]"]').val('');
        form.find('input[name="imageRespDesc[1]"]').val('');
        form.find('input[name="choiseText[2]"]').val('');
        form.find('input[name="imageRespDesc[2]"]').val('');
        form.find('input[name="choiseText[3]"]').val('');
        form.find('input[name="imageRespDesc[3]"]').val('');   
        return;     
    }
    for (let index = 0; index < choiseText.length; ++index) {
        if (data.REPONSES[index] == undefined) {
            continue;
        }
        form.find('input[name="choiseText['+index+']"]').val(data.REPONSES[index].CONTENU);
        form.find('input[name="imageRespDesc['+index+']"]').val(data.REPONSES[index].IMG_LABEL); 
        form.find('input[name="response['+index+']"]').val(data.REPONSES[index].ID);       
    }
}

function displayInputsByType(form,type) {
    switch(type) {
        case 'freeText':
            form.find('.reponse_group').hide();
            form.find('.reponse_group ~ label').hide();
            break;
        case 'slider':
            form.find('.reponse_group').show();
            form.find('.reponse_group .admin-form_group').hide();
            form.find('input[name="slider"]').parent('.admin-form_group').show();
            break;
        case 'textChoice':
            form.find('.reponse_group').show();
            form.find('.reponse_group .admin-form_group').hide();
            form.find('.reponse_group .admin-form_group').each(function(index) {
                if ($(this).find('input').attr('name').match(/^choiseText\[\d+\]$/)) {
                   $(this).show(); 
                }
            });
            break;
        case 'multiImage':
            form.find('.reponse_group').show();
            form.find('.reponse_group .admin-form_group').hide();
            form.find('.reponse_group .admin-form_group').each(function(index) {
                if ($(this).find('input').attr('name').match(/^imageResp\[\d+\]$/) || $(this).find('input').attr('name').match(/^imageRespDesc\[\d+\]$/)) {
                   $(this).show(); 
                }
            });
            break;
        default:
            form.find('.reponse_group').show();
            form.find('.reponse_group .admin-form_group').show();
    }
}
