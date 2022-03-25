/**
 * Final loader after all slides
 */
function calculateLoader(clientCode) {
    var can = document.getElementById('canvas'),
        spanPercent = document.getElementById('percent'),
        spanPercentNumber = document.getElementById('percent-number'),
        spanPercentPercent = document.getElementById('percent-percent'),
        c = can.getContext('2d');

    var posX = can.width / 2,
        posY = can.height / 2,
        fps = 2000 / 300,
        percent = 0,
        onePercent = 360 / 100,
        result = onePercent * 100;

    c.lineCap = 'round';
    arcMove();

    function arcMove() {
        var deegres = 0;
        var acrInterval = setInterval(function () {
            deegres += 1;
            c.clearRect(0, 0, can.width, can.height);
            percent = deegres / onePercent;

            spanPercentNumber.innerHTML = percent.toFixed();
            spanPercentPercent.innerHTML = '%';

            c.beginPath();
            c.arc(posX, posY, 100, (Math.PI / 180) * 270, (Math.PI / 180) * (270 + 360));
            c.strokeStyle = '#E9EDF3';
            c.lineWidth = '10';
            c.stroke();

            c.beginPath();
            c.strokeStyle = '#027AFF';
            c.lineWidth = '10';
            c.arc(posX, posY, 100, (Math.PI / 180) * 270, (Math.PI / 180) * (270 + deegres));
            c.stroke();
            if (deegres >= result) {
                clearInterval(acrInterval);
                window.location.replace(window.nextPageUrl + '/' + clientCode);
            }
        }, fps);
    }
}
