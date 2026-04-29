const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// Helper methods
Toast.success = function(message) {
    return this.fire({
        icon: 'success',
        title: message
    });
};

Toast.error = function(message) {
    return this.fire({
        icon: 'error',
        title: message
    });
};

Toast.warning = function(message) {
    return this.fire({
        icon: 'warning',
        title: message
    });
};

Toast.info = function(message) {
    return this.fire({
        icon: 'info',
        title: message
    });
};

window.Toast = Toast;
