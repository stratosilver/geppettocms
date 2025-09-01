document.querySelectorAll('[contenteditable]').forEach(function(element) {
    element.addEventListener('input', function(event) {
        document.getElementById('edit_field_'+event.target.id).value = event.target.innerHTML;
        //console.log('Id élement changé:', event.target.id);
        console.log('Element changé:', document.getElementById('edit_field_'+event.target.id).value);
    });
});

