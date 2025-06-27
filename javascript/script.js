document.addEventListener("DOMContentLoaded", function() {
    const roleSelect = document.getElementById("userRole");
    const documentGroup = document.getElementById("grupoDocumento");
    const userGroup = document.getElementById("grupoUsuario");
    const usernameField = document.getElementById("usernameField");
    const userField = document.getElementById("userField");
    const passwordField = document.getElementById("passwordField");

    // Función para manejar la visibilidad de campos según el rol
    function handleRoleChange() {
        const role = roleSelect.value;
        
        if (role === "student") {
            documentGroup.style.display = "flex";
            userGroup.style.display = "none";
            usernameField.required = true;
            userField.required = false;
        } else {
            documentGroup.style.display = "none";
            userGroup.style.display = "flex";
            usernameField.required = false;
            userField.required = true;
        }
    }

    // Event listener para cambios en el select de rol
    roleSelect.addEventListener("change", handleRoleChange);

    // Inicializar campos al cargar la página
    handleRoleChange();

    // Mostrar/ocultar contraseña (si tienes este botón)
    const togglePassword = document.getElementById("togglePassword");
    if (togglePassword) {
        togglePassword.addEventListener("click", function() {
            const type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;
            this.classList.toggle("fa-eye-slash");
        });
    }
});