jQuery(document).ready(function ($) {
    
    $('#quiz_form').submit(function(event) {
        event.preventDefault();
        
        // Сбор данных формы
        var formData = $(this).serializeArray();
        formData.push({name: 'action', value: 'feedback_action'});
        formData.push({name: 'nonce', value: qu_feedback_object.nonce});
        
        let newArr = {};
        
        $('.quiz_section_item').each(function(index) {
            let title = $(this).find('.quiz_question_text').text();
            var sList = "";
            $(this).find('input').each(function(index) {
                if (!$(this).hasClass("art_anticheck")) {
                    sList += this.checked ? $(this).val() + ', ' : "";
                }
            });
            // Убираем последнюю запятую и пробел
            newArr[title] = sList.slice(0, -2);
        });
        
        formData.push({name: 'quizResults', value: JSON.stringify(newArr)});
        console.log('xx');
        
        $.ajax({
            url: qu_feedback_object.url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Обработка успешной отправки
                    alert('Сообщение отправлено успешно.');
                    $('#quiz_form')[0].reset(); // Сброс формы
                } else {
                    // Обработка ошибок
                    alert('Ошибка отправки: ' + response.data);
                }
            },
            error: function() {
                // Обработка ошибок при выполнении запроса
                alert('Ошибка выполнения AJAX запроса');
            }
        });
    });

});
