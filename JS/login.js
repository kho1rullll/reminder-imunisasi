// Toggle mata password
const eyeBtn = document.getElementById('eyeToggle');
const passInput = document.getElementById('password');
if(eyeBtn && passInput){
    eyeBtn.addEventListener('click', () => {
        const isPass = passInput.type === 'password';
        passInput.type = isPass ? 'text' : 'password';
        eyeBtn.textContent = isPass ? '🙈' : '👁️';
    });
}