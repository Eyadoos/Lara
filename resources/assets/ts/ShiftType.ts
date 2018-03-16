import * as $ from "jquery";
import {translate} from "./Translate";
import * as bootbox from "bootbox";

$(window).on('load', () => {
  $('.shiftTypeSelector').selectpicker({
    style: 'btn btn-default btn-sm',
    liveSearch: true
  });
  $('.shiftTypeReplaceSelector').selectpicker({
    style: 'btn btn-default btn-sm',
    liveSearch: true
  });
});

$('.shiftTypeSelector').change((event) => {
  let selectElement = $(event.target);
  let selectedValue = selectElement.val();
  if (selectedValue == -1) {
    return;
  }
  $(event.target).parents('form').submit();
});

$('.shiftTypeReplaceSelector').change((event) => {
  let selectElement = $(event.target);
  let selectedValue = selectElement.val();
  if (selectedValue == -1) {
    return;
  }
  let submitBtn = $(event.target).parents('form');
  let shiftName = $(event.target).parents('form').children('input[name="shiftName"]').val();

  // Initialise modal and show loading icon and message
  bootbox.confirm({
    title: '<h4 class="alert alert-danger text-center">' + translate('deleteTemplate') + '</h4>',
    size: 'large',
    message: '<p>' + translate('replaceShiftTypeConfirmation') + '</p><p class="text-danger">' + shiftName + '</p>',
    buttons: {
      confirm: {
        label: '<span class="glyphicon glyphicon-ok" ></span>' + translate('replaceAll'),
        className: 'btn-success'
      },
      cancel: {
        label: '<span class="glyphicon glyphicon-remove" ></span>',
        className: 'btn-default'
      }
    },
    callback: (result) => {
      if (result) {
        submitBtn.submit();
      }
    }
  });
});