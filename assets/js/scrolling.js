// scrolling.js
document.addEventListener("DOMContentLoaded", function() {
    const tradeList = document.getElementById('trade-updates-list');
    let clone = tradeList.cloneNode(true);
    tradeList.parentNode.appendChild(clone);
    tradeList.style.animationDuration = `${tradeList.childElementCount * 1}s`;
});
