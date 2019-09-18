<?php



function GetRealUserIp($default = NULL, $filter_options = 12582912) {
    $HTTP_X_FORWARDED_FOR = isset($_SERVER)? $_SERVER["HTTP_X_FORWARDED_FOR"]:getenv('HTTP_X_FORWARDED_FOR');
    $HTTP_CLIENT_IP = isset($_SERVER)?$_SERVER["HTTP_CLIENT_IP"]:getenv('HTTP_CLIENT_IP');
    $HTTP_CF_CONNECTING_IP = isset($_SERVER)?$_SERVER["HTTP_CF_CONNECTING_IP"]:getenv('HTTP_CF_CONNECTING_IP');
    $REMOTE_ADDR = isset($_SERVER)?$_SERVER["REMOTE_ADDR"]:getenv('REMOTE_ADDR');

    $all_ips = explode(",", "$HTTP_X_FORWARDED_FOR,$HTTP_CLIENT_IP,$HTTP_CF_CONNECTING_IP,$REMOTE_ADDR");
    foreach ($all_ips as $ip) {
        if ($ip = filter_var($ip, FILTER_VALIDATE_IP, $filter_options))
            break;
    }
    return $ip?$ip:$default;
}

$ip = GetRealUserIp();

//Something to write to txt log
$log  = "User: ".$ip.' - '.date("F j, Y, g:i a").PHP_EOL.
    "-------------------------".PHP_EOL;
//Save string to log, use FILE_APPEND to append.
file_put_contents('./log_'.date("j.n.Y").'.txt', $log, FILE_APPEND);




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StitchMe Automation</title>
    <link rel="stylesheet" href="styles/reset.css">
    <link rel="stylesheet" href="styles/stitchme_automation.css">

    <script src="scripts/pdfmake.min.js"></script>
    <script src="scripts/vfs_fonts.js"></script>

    <script src="scripts/templates.js"></script>
    <script src="scripts/stitchme.js"></script>


</head>
<body>
<div id="wrapper">

    <section id="controls">
        <header>
            <img src="images/stitchme_automation.png" width="318"/>
        </header>
        <div id="thumbcontrols">
            <input id="thumbnailload" type="file" onchange="loadThumbnail();" accept=".jpg,.jpeg, .png,"/>
            <p id="thumbdimensions">0px x 0px @ 10 stitches/in = 0in x 0in</p>
            <div id="thumbs">
                <div id="originalthumbcontainer">
                    <canvas width="120" height="120" id="originalthumbcanvas"></canvas>
                </div>
                <div id="contrastthumbcontainer">
                    <canvas width="120" height="120" id="contrastthumbcanvas"></canvas>
                </div>
            </div>
            <div id="chartcontrols">
                <table id="charttable">

                </table>
            </div>
        </div>
        <div id="pageControls">
            <fieldset id="template">
                <legend>Document Template</legend>
                <select id="docTemplate">
                    <option value="5x7l">5x7 Landscape</option>
                    <option value="5x7p">5x7 Portrait</option>
                    <option value="8x10l">8x10 Landscape</option>
                    <option value="8x10p">8x10 Portrait</option>
                    <option value="10x10l">10x10 Landscape</option>
                    <option value="10x10p">10x10 Portrait</option>
                </select>
                <p id="docInfo">A4 Landscape</p>


            </fieldset>
            <fieldset id="margins">
                <legend>Margins</legend>
                <label>Left
                    <input id="marginLeft" type="text" class="margininput" value="32"/>
                </label>
                <label>Top
                    <input id="marginTop" type="text" class="margininput" value="32"/>
                </label>
                <label>Right
                    <input id="marginRight" type="text" class="margininput" value="32"/>
                </label>
                <label>Bottom
                    <input id="marginBottom" type="text" class="margininput" value="32"/>
                </label>
            </fieldset>
            <fieldset id="pages">
                <legend>Pages</legend>
                <div id="pageOptions">
                    <table id="pageOptionsTable">
                        <tbody>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Page</th>
                            <th><span class="radioLbl">Wool</span>|<span class="radioLbl">Contrast</span>|<span
                                    class="radioLbl">Chart</span></th>
                            <th><span class="gridLbl">Grid</span> | <span class="gridLbl">None</span> | <span
                                    class="gridLbl">All</span> | <span class="gridLbl">Major</span></th>
                            <th>Swatch | <span class="swatchLbl">L</span> | <span class="swatchLbl">T</span> | <span
                                    class="swatchLbl">R</span> | <span class="swatchLbl">B</span> |
                            </th>
                        </tr>
                        <tr>
                            <td id="page1">Aida Print</td>
                            <td><input id='page1print' type="checkbox" checked/></td>
                            <td>
                                <input id="page1wool" class="radioLbl" name="page1content" type="radio" value="wool"/>
                                <input id="page1contrast" class="radioLbl" name="page1content" type="radio"
                                       value="contrast" checked/>
                                <input id="page1chart" class="radioLbl" name="page1content" type="radio" value="chart"/>
                            </td>
                            <td>
                                <input id="page1ShowGrid" class="gridLbl" type="checkbox"/>
                                <input id="page1GridNone" class="gridLbl" type="radio" name="page1GridOptions"
                                       value="none"/>
                                <input id="page1GridAll" class="gridLbl" type="radio" name="page1GridOptions"
                                       value="all"/>
                                <input id="page1GridMajor" class="gridLbl" type="radio" name="page1GridOptions"
                                       value="major"/>
                            </td>
                            <td>
                                <input id="page1ShowSwatch" class="swatchCheck" type="checkbox" checked/>
                                <input id="page1SwatchLeft" class="swatchLbl" type="radio" name="page1SwatchOptions"
                                       value="L" checked/>
                                <input id="page1SwatchTop" class="swatchLbl" type="radio" name="page1SwatchOptions"
                                       value="T"/>
                                <input id="page1SwatchRight" class="swatchLbl" type="radio" name="page1SwatchOptions"
                                       value="R"/>
                                <input id="page1SwatchBottom" class="swatchLbl" type="radio" name="page1SwatchOptions"
                                       value="B"/>
                            </td>
                        </tr>
                        <tr>
                            <td id="page2">Wool</td>
                            <td><input id='page2print' type="checkbox" checked/></td>
                            <td>
                                <input id="page2wool" class="radioLbl" name="page2content" type="radio" value="wool"
                                       checked/>
                                <input id="page2contrast" class="radioLbl" name="page2content" type="radio"
                                       value="contrast"/>
                                <input id="page2chart" class="radioLbl" name="page2content" type="radio" value="chart"/>
                            </td>
                            <td>
                                <input id="page2ShowGrid" class="gridLbl" type="checkbox" checked/>
                                <input id="page2GridNone" class="gridLbl" type="radio" name="page2GridOptions"
                                       value="none"/>
                                <input id="page2GridAll" class="gridLbl" type="radio" name="page2GridOptions"
                                       value="all"/>
                                <input id="page2GridMajor" class="gridLbl" type="radio" name="page2GridOptions"
                                       value="major" checked/>
                            </td>
                            <td>
                                <input id="page2ShowSwatch" class="swatchCheck" type="checkbox" checked/>
                                <input id="page2SwatchLeft" class="swatchLbl" type="radio" name="page2SwatchOptions"
                                       value="L"/>
                                <input id="page2SwatchTop" class="swatchLbl" type="radio" name="page2SwatchOptions"
                                       value="T"/>
                                <input id="page2SwatchRight" class="swatchLbl" type="radio" name="page2SwatchOptions"
                                       value="R" checked/>
                                <input id="page2SwatchBottom" class="swatchLbl" type="radio" name="page2SwatchOptions"
                                       value="B"/>
                            </td>
                        </tr>
                        <tr>
                            <td id="page3">Contrast</td>
                            <td><input id='page3print' type="checkbox" checked/></td>
                            <td>
                                <input id="page3wool" class="radioLbl" name="page3content" type="radio" value="wool"/>
                                <input id="page3contrast" class="radioLbl" name="page3content" type="radio"
                                       value="contrast" checked/>
                                <input id="page3chart" class="radioLbl" name="page3content" type="radio" value="chart"/>
                            </td>
                            <td>
                                <input id="page3ShowGrid" class="gridLbl" type="checkbox" checked/>
                                <input id="page3GridNone" class="gridLbl" type="radio" name="page3GridOptions"
                                       value="none"/>
                                <input id="page3GridAll" class="gridLbl" type="radio" name="page3GridOptions"
                                       value="all" checked/>
                                <input id="page3GridMajor" class="gridLbl" type="radio" name="page3GridOptions"
                                       value="major"/>
                            </td>
                            <td>
                                <input id="page3ShowSwatch" class="swatchCheck" type="checkbox" checked/>
                                <input id="page3SwatchLeft" class="swatchLbl" type="radio" name="page3SwatchOptions"
                                       value="L"/>
                                <input id="page3SwatchTop" class="swatchLbl" type="radio" name="page3SwatchOptions"
                                       value="T"/>
                                <input id="page3SwatchRight" class="swatchLbl" type="radio" name="page3SwatchOptions"
                                       value="R"/>
                                <input id="page3SwatchBottom" class="swatchLbl" type="radio" name="page3SwatchOptions"
                                       value="B" checked/>
                            </td>
                        </tr>
                        <tr>
                            <td id="page4">Chart</td>
                            <td><input id='page4print' type="checkbox" checked/></td>
                            <td>
                                <input id="page4wool" class="radioLbl" name="page4content" type="radio" value="wool"/>
                                <input id="page4contrast" class="radioLbl" name="page4content" type="radio"
                                       value="contrast"/>
                                <input id="page4chart" class="radioLbl" name="page4content" type="radio" value="chart"
                                       checked/>
                            </td>
                            <td>
                                <input id="page4ShowGrid" class="gridLbl" type="checkbox"/>
                                <input id="page4GridNone" class="gridLbl" type="radio" name="page4GridOptions"
                                       value="none"/>
                                <input id="page4GridAll" class="gridLbl" type="radio" name="page4GridOptions"
                                       value="all"/>
                                <input id="page4GridMajor" class="gridLbl" type="radio" name="page4GridOptions"
                                       value="major"/>
                            </td>
                            <td>
                                <input id="page4ShowSwatch" class="swatchCheck" type="checkbox"/>
                                <input id="page4SwatchLeft" class="swatchLbl" type="radio" name="page4SwatchOptions"
                                       value="L"/>
                                <input id="page4SwatchTop" class="swatchLbl" type="radio" name="page4SwatchOptions"
                                       value="T"/>
                                <input id="page4SwatchRight" class="swatchLbl" type="radio" name="page4SwatchOptions"
                                       value="R"/>
                                <input id="page4SwatchBottom" class="swatchLbl" type="radio" name="page4SwatchOptions"
                                       value="B"/>
                            </td>
                        </tr>

                        </tbody>

                    </table>


                </div>


            </fieldset>
            <!--<fieldset id="swatches">
                <legend>Swatches</legend>
                <label>Width
                    <input id="swatchWidth" type="text" class="swatchInput" value="32"/>
                </label>
                <label>Height
                    <input id="swatchHeight" type="text" class="swatchInput" value="auto"/>
                </label>
                <label>Group Pos X
                    <input id="swatchGroupPosX" type="text" class="swatchInput" value="200"/>
                </label>
                <label>Group Pos Y
                    <input id="swatchGroupPosY" type="text" class="swatchInput" value="100"/>
                </label>
            </fieldset>
            <fieldset id="stitches">
                <legend>Stiches</legend>
                <label>Width
                    <input id="stitchWidth" type="text" class="stitchInput" value="7.2"/>
                </label>
                <label>Height
                    <input id="stitchHeight" type="text" class="stitchInput" value="7.2"/>
                </label>
                <label>Image X
                    <input id="stitchImageX" type="text" class="stitchInput" value="20"/>
                </label>
                <label>Image Y
                    <input id="stitchImageY" type="text" class="stitchInput" value="100"/>
                </label>
            </fieldset>-->
            <button id="renderDoc" onclick="renderDocument()">Create Document</button>
        </div>

    </section>
    <section id="pdfdisplay">
        <iframe id="pdfFrame">

        </iframe>
    </section>
    <footer></footer>

</div>
</body>
</html>