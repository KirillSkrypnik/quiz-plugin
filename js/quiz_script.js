$(document).on("click", 'input.quiz_radio_hidden', function () {
    let nameInput = $(this).attr('name');
    let labelName = $(this).parent().attr('data-name');
    let dataName = "label[data-name$='" + labelName +"']";
      if ($(this).is(":checked") && $(this).attr('type') !== 'checkbox') {
        $(dataName).removeClass('checked_parent');
        $(this).parent().addClass('checked_parent');
        setTimeout(function () {
            $('.quiz_button_next').trigger( "click");
        }, 1000);
      } else if ( $(this).attr('type') == 'checkbox') {
        if ($(this).is(":checked")) {
            $(this).parent().addClass('checked_parent');
        } else {
            $(this).parent().removeClass('checked_parent');
        }
      }
});

$(document).on("click", 'input.quiz_radio_social', function () {
    let nameInput = $(this).attr('id');
    $('.social_label ').removeClass('social_label_active');
    $("label[for=" + nameInput + "]").addClass('social_label_active');
});




 $(document).ready(function(){
    /*Status bar*/
    /*Статус бар*/
    let statusItemCount = Number($('.progress_quiz_bar').attr('data-loaditem'));
    let statusItemStep = (100/(statusItemCount-1)).toFixed(2);
    /**/
    let statusBar = 0;
    
    /*New interest*/
    /*Новые проценты*/
    let itemQuantity = $('.quiz_section_item').length;
    let itemQuantityStep = (100/(itemQuantity-1)).toFixed(0);
    let indexLastItem = itemQuantity-1;
    $('.quiz_section_item').each(function( index ) {
        let dataItem = Number($(this).attr('data-item'));
        if (index == indexLastItem){
              $(this).attr('data-itemQuantity', 100); 
        }else {
            $(this).attr('data-itemQuantity', (dataItem-1)*itemQuantityStep); 
        }
    });
    /*Конец новых процентов*/
    /*End of new interest*/
    
     
    $('.quiz_button_next').click(function() {
        let allItem = $('.quiz_form .quiz_section_item');
        let length = allItem.length;
        let activeStep = $('.quiz_section_item_active');
        $(activeStep).find('input').each(function( index ) {
            if ($(this).is(":checked") && $(this).attr('type') !== 'checkbox') {
                
                // Check to see if one of the options is selected
                // Проверка на выбор одного из вариантов
                let activeStepItem = Number($(activeStep).attr('data-item'));
                
                /*Find out the height of the next active block from the parameter*/
                /*Узнаем высоту следующего активного блока из параметра*/
                let nextStepHeight = Number($(activeStep).next().attr('data-height'));
                /**/
                
                /*For the bar status, we find out the current step number*/
                /*Для статус бара узнаем актуальный номер шага*/
                let statusBarNext = Number($(activeStep).next().attr('data-item') - 1);
                $('.progress_quiz_bar_active').css('width', (statusBarNext*statusItemStep) + '%');
                
                /*And also display the percentage in a bar*/
                /*А так же выводим процент в бар*/
                let dataItemquantity = $(activeStep).next().attr('data-itemquantity');
                $('.quiz_form_percent').text(dataItemquantity);
                /**/
                
                $(activeStep).next().addClass('quiz_section_item_active');
                if ($(activeStep).next().hasClass('quiz_section_item_final')){
                    $('.quiz_button_next').addClass('quiz_button_next_none');
                } else {
                    $('.quiz_button_next').removeClass('quiz_button_next_none');
                }
                /**/
                
                /*We actually display the height*/
                /*Собственно выводим высоту*/
                if (nextStepHeight === 0){
                	$('.quiz_section_item_wrapper').css('height', 100+'%');
                } else if(nextStepHeight < 0) {
                    $('.quiz_section_item_wrapper').css('height', 100+'%');
                } else {
                	$('.quiz_section_item_wrapper').css('height', nextStepHeight+'px');    
                }
                /**/
                
                if (activeStepItem !== length) {
                    $(activeStep).removeClass('quiz_section_item_active');  
                } 
                if(activeStepItem > 0){
                    $('.quiz_button_prev').addClass('show_button_prev');
                } else {
                    $('.quiz_button_prev').removeClass('show_button_prev');
                }
                // 
            } else if ($(this).attr('type') == 'checkbox') {
                
                // Check to see if one of the options is selected
                // Проверка на выбор одного из вариантов
                
                let activeStepItem = Number($(activeStep).attr('data-item'));
                
                /*Find out the height of the next active block from the parameter*/
                /*Узнаем высоту следующего активного блока из параметра*/
                let nextStepHeight = Number($(activeStep).next().attr('data-height'));
                /**/
                
                /*For the bar status, we find out the current step number*/
                /*Для статус бара узнаем актуальный номер шага*/
                let statusBarNext = Number($(activeStep).next().attr('data-item') - 1);
                $('.progress_quiz_bar_active').css('width', (statusBarNext*statusItemStep) + '%');
                /**/
                
                /*And also display the percentage in a bar*/
                /*А так же выводим процент в бар*/
                let dataItemquantity = $(activeStep).next().attr('data-itemquantity');
                $('.quiz_form_percent').text(dataItemquantity);
                /**/
                
                $(activeStep).next().addClass('quiz_section_item_active');
                if ($(activeStep).next().hasClass('quiz_section_item_final')){
                    $('.quiz_button_next').addClass('quiz_button_next_none');
                } else {
                    $('.quiz_button_next').removeClass('quiz_button_next_none');
                }
                
                
                /**/
                /*Output height*/
                /*Выводим высоту*/
                if (nextStepHeight === 0){
                	$('.quiz_section_item_wrapper').css('height', 100+'%');
                } else if(nextStepHeight < 0) {
                    $('.quiz_section_item_wrapper').css('height', 100+'%');
                } else {
                	$('.quiz_section_item_wrapper').css('height', nextStepHeight+'px');    
                }
                /**/
                if (activeStepItem !== length) {
                    $(activeStep).removeClass('quiz_section_item_active');  
                } 
                if(activeStepItem > 0){
                    $('.quiz_button_prev').addClass('show_button_prev');
                } else {
                    $('.quiz_button_prev').removeClass('show_button_prev');
                }
            }
        });

    });
    
    $('.quiz_button_prev').click(function() {
        let allItem = $('.quiz_form .quiz_section_item');
        let length = allItem.length;
        let activeStep = $('.quiz_section_item_active');
        let activeStepItem = Number($(activeStep).attr('data-item'));
        
        /*Find out the height of the previous active block from the parameter*/
        /*Узнаем высоту предыдущего активного блока из параметра*/
        let prevStepHeight = Number($(activeStep).prev().attr('data-height'));
        /**/
        
        /*Find out the current width of the bar status*/
        /*Узнаем действующую ширину статус бара*/
        let statusBarValue = Number($('.progress_quiz_bar_active').css('width').slice(0, -2)).toFixed(2);
        
        /*For the bar status, we find out the current step number*/
        /*Для статус бара узнаем актуальный номер шага*/
        let statusBarPrev = Number($(activeStep).prev().attr('data-item'));
        let statusDegree = statusBarValue/statusBarPrev;
        $('.progress_quiz_bar_active').css('width', (statusBarValue-statusDegree) + 'px');
        
        /*Here are percentages*/
        /*Тут проценты*/
        let dataItemquantity = $(activeStep).prev().attr('data-itemquantity');
        $('.quiz_form_percent').text(dataItemquantity);
        /**/
        
        /*Возвращаем предыдущее*/
        /*Returning the previous*/
        $(activeStep).prev().addClass('quiz_section_item_active');
        if ($(activeStep).prev().hasClass('quiz_section_item_final')){
            $('.quiz_button_next').removeClass('quiz_button_next_none');
        } else {
            $('.quiz_button_next').removeClass('quiz_button_next_none');
        }
        
        
        /*We actually display the height*/
        /*Собственно выводим высоту*/
        if(prevStepHeight === 0){
            $('.quiz_section_item_wrapper').css('height', 100+'%');
        } else {
        	$('.quiz_section_item_wrapper').css('height', prevStepHeight+'px');
        }
        /**/
        if (activeStepItem !== 1) {
            $(activeStep).removeClass('quiz_section_item_active');  
        } 
    });
    
    /*Height of the block (It is not yet clear whether this will be useful)*/
    /*Высота блока (Пока не понятно пригодится ли)*/
    let maximum = null;    
    let minimum = 150;  
    $( ".quiz_section_item" ).each(function( index ) {
        let itemHeight = $(this).height();
        $(this).attr('data-height', itemHeight);
        maximum = (itemHeight > maximum) ? itemHeight : maximum;
        minimum = (itemHeight < minimum) ? itemHeight : minimum;
    });
    $('.quiz_section_item_wrapper').css('height', maximum+'px');
    
 });
