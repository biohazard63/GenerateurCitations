function exporterCitationEnImage(citation, auteur, index) {
    var canvas = document.createElement('canvas');
    canvas.width = 900;
    canvas.height = 300;

    var ctx = canvas.getContext('2d');

    ctx.fillStyle = 'black';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.fillStyle = 'white';
    ctx.font = '30px Arial';
    ctx.weight = 'bold';
    ctx.textAlign = 'center';

    var maxLineWidth = 500;
    var lineHeight = 40;
    var words = citation.split(' ');
    var line = '';
    var y = 80;

    for (var n = 0; n < words.length; n++) {
        var testLine = line + words[n] + ' ';
        var metrics = ctx.measureText(testLine);
        var testWidth = metrics.width;
        if (testWidth > maxLineWidth && n > 0) {
            ctx.fillText(line, canvas.width / 2, y);
            line = words[n] + ' ';
            y += lineHeight;
        } else {
            line = testLine;
        }
    }
    ctx.fillText(line, canvas.width / 2, y);

    ctx.fillText('" ' + auteur + ' "', canvas.width / 2, y + lineHeight);

    var dataUrl = canvas.toDataURL('image/jpeg');

    var link = document.createElement('a');
    link.href = dataUrl;
    link.download = 'citation' + index + '.jpg';

    link.click();
}

var exportButtons = document.querySelectorAll('.exportButton');
exportButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        var index = this.getAttribute('data-index');
        var citation = decodeURIComponent(this.getAttribute('data-citation'));
        var auteur = this.getAttribute('data-auteur');
        exporterCitationEnImage(citation, auteur, index);
    });
});