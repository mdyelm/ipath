<html>
    <head>
        <meta charset="UTF-8" />
        <style>
            //style dowload
            #contentPdf {
                width: 100%;
            }
            h1 {
                text-align: center;
                border-bottom-style: double;
                margin-bottom: 50px;
                font-size: 20px;
            }
            table {
                border-spacing: 0;
                border-collapse: collapse;
            }
            td,
            th {
                padding: 0;
            }
            table {
                background-color: transparent;
            }
            th {
                text-align: left;
            }
            td, th {
                padding: 0;
            }
            #contentPdf .row{
                padding-bottom: 40px;
                width: 50%;
                float: left;
                overflow: hidden;
            }
            #contentPdf .viewDes{
                text-align: center;
                display: inline-block;
                overflow: hidden;
                width: 100%;
            }
            #contentPdf .viewDes table{
                font-size: 10px;
                text-align: center;
                display: inline-block;
                width: 80%;
            }
            #contentPdf .viewImg{
                width: 100%;
                padding-bottom: 10px;
                text-align: center;
                display: inline-block;
            }
            #contentPdf .row .viewImg img{
                width: 80%;
                height: 150px;
            }
            .TALeft {
                text-align: left;
            }
            .TACenter {
                text-align: center;
            }
            table, th, td {
                border: 1px solid black;
                padding: 3px;
            }
            .clear {
                clear: both;
            }
            .tdCM {
                height: 60px;
            }
            //end style dowload
        </style>
    </head>
    <body>
    <?= $this->fetch('content') ?>
    </body>
</html>


