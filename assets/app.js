/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import 'bootstrap/dist/js/bootstrap.min'

if (document.getElementsByName("reservation")!=null){
    document.getElementById("reservation_forfait").addEventListener('change', (event) => {
        DisplayForfait();
    });
    DisplayForfait();
}
function DisplayForfait() {
    let seletedValue = document.getElementById("reservation_forfait").value;
    let selectedTitle = document.getElementsByTagName("option")[seletedValue - 12].textContent;
    let forfaits = document.getElementsByClassName("forfait")[0].children;
    for (let i = 0; i <forfaits.length; i++) {
        if (selectedTitle === forfaits[i].firstElementChild.nextElementSibling.firstElementChild.innerText) {
            forfaits[i].style.display = "block"
        } else {
            forfaits[i].style.display = "none"
        }
    }
}