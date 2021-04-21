/*
    未经授权禁止转卖，否则将依法维权
*/

var child_process = require("child_process");

var url = "https://github.com/DOUBLE-Baller/momo",
    cmd = '';
console.log(process.platform)
switch (process.platform) {
    case 'win32':
        cmd = 'start';
        child_process.exec(cmd + ' ' + url);
        break;

    case 'darwin':
        cmd = 'open';
        child_process.exec(cmd + ' ' + url);
        break;
}