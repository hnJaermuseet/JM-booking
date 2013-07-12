// Script used by roomlist.php

$(document).ready(function() {
    var roomlist = $('#roomlist');
    $('input[type=checkbox]', roomlist).change(function() {
        // On of the boxes has changed, lets put together the areas and rooms selected and submit

        // If all rooms is selected and we clicked on another room, lets uncheck the all rooms checkbox
        if(this.value !== '0') {
            $('input[type=checkbox][name=roomlist_roomSelector][value=\'0\']', roomlist).attr('checked', false);
        }


        var selectedAreas = $('input[type=checkbox][name=roomlist_areaSelector]:checked')
            .map(function() {return this.value;}).get().join(',');
        var selectedRooms = $('input[type=checkbox][name=roomlist_roomSelector]:checked')
            .map(function() {return this.value;}).get().join(',');

        var roomlistform = $('#roomlistForm');
        $('input[name=area]', roomlistform).attr('value', selectedAreas);
        $('input[name=room]', roomlistform).attr('value', selectedRooms);
        roomlistform.submit();
    });
});