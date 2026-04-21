// Toggle mata password
const eyeReg = document.getElementById('eyeReg');
const passReg = document.getElementById('password');
if(eyeReg && passReg) {
    eyeReg.addEventListener('click', () => {
        const show = passReg.type === 'password';
        passReg.type = show ? 'text' : 'password';
        eyeReg.textContent = show ? '🙈' : '👁️';
    });
}