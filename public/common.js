
const responSwalAlert = (position, icon, message) => {
    Swal.fire({
        position: `top-${position}`,
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: 1500
    })
}

const swalConfirmasion = (text,callback) => {
    Swal.fire({
        title: 'Apakah Benar?',
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                callback()
            }
        })
}

const removeXhr = () => {
    $(".invalid-feedback").remove()
    $(".text-danger").remove()
    $(".was-validated").removeClass()
}

const handleErrorXhr = (xhr) => {
    if (xhr.status == 422) {
        let errorLoop = Object.entries(xhr.responseJSON.errors)
        console.info(xhr.responseJSON.errors)
        errorLoop.forEach((val, key) => {
            let resRequired = val[1].find((message) => {
                return message.includes('required')
            })

            $(`#${val[0]}`).closest('div').append(`<div class="${resRequired ? 'invalid-feedback' : 'text text-danger'}">${val[1]}</div>`)
        });
    }
}
