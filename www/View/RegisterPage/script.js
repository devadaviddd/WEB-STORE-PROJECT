const left = document.querySelector('.customer');
const middle = document.querySelector('.vendor');
const right = document.querySelector('.shipper');
const buttons = document.querySelectorAll('.btn');


left.addEventListener('mouseenter', () => {
    left.classList.add('hover');
})

left.addEventListener('mouseleave', () => {
    left.classList.remove('hover');
})


middle.addEventListener('mouseenter', () => {
    middle.classList.add('hover');
}) 

middle.addEventListener('mouseleave', () => {
    middle.classList.remove('hover');
}) 



right.addEventListener('mouseenter', () => {
    right.classList.add('hover');
}) 

right.addEventListener('mouseleave', () => {
    right .classList.remove('hover');
}) 


buttons.forEach(button => {
    button.addEventListener('click', (e) => {
        const x = e.clientX;
        const y = e.clientY;

        const buttonTop = e.target.top;
        const buttonLeft = e.target.left;

        const xInside = x + buttonLeft;
        const yInside = y + buttonTop;

        const circle = document.createElement('span');
        circle.classList.add('circle');
        circle.style.top = yInside + 'px';
        circle.style.left = xInside + 'px';

        e.target.appendChild(circle);

        // Remove the span circle
        setTimeout(() => circle.remove(), 500)
    })
})
