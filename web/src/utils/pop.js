import swal from 'sweetalert';

const pop = {
  loading: () => {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border text-primary';
    spinner.setAttribute('role', 'status');
    spinner.innerHTML = '<span class="sr-only"></span>';

    swal({
      title: '',
      text: 'Please wait a moment',
      content: spinner,
      buttons: false,
      closeOnClickOutside: false,
    });
  },

  confirm: (title, text, callback, cancelCallback) => {
    return swal({
      title: title,
      text: text,
      icon: "warning",
      closeOnClickOutside: false,
      closeOnEsc: false,
      dangerMode: true,
      buttons: {
        cancel: "Cancel",
        confirm: "Confirm",
      },
    }).then(confirm => {
      if (confirm) {
        if (callback && typeof callback === "function") callback();
      } else {
        if (cancelCallback && typeof cancelCallback === "function") cancelCallback();
      }
    });
  },

  success: (title = 'Success') => {
    swal({
      title: title,
      text: 'Operation completed successfully',
      icon: 'success',
      buttons: false,
      closeOnClickOutside: false,
      timer: 1000 
    });
  },

  error: (message) => {
    swal({
      title: message,
      text: "Please try again later",
      icon: 'error',
      buttons: {
        confirm: {
          text: "OK",
          value: true,
          visible: true,
          className: "btn btn-primary"
        }
      },
      closeOnClickOutside: false,
    });
  },

  close: () => {
    swal.close();
  }
};

export default pop;
