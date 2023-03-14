$('#light-dark-mode-toggle-button').click(()=>{
    console.log('light-dark-mode-toggle-button.click');
    // toggle html class selector
    $('html').toggleClass('html-light html-dark')
    // toggle img class selector
    $('img').toggleClass('img-light img-dark')

    // toggle i class selector
    $('#light-dark-mode-toggle-button').toggleClass('fa-toggle-off fa-toggle-on')
    

})

