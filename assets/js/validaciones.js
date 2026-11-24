document.querySelectorAll('.validar-formulario').forEach(function (formulario) {
    formulario.addEventListener('submit', function (evento) {
        if (!formulario.checkValidity()) {
            evento.preventDefault();
            evento.stopPropagation();
        }
        formulario.classList.add('was-validated');
    });
});
