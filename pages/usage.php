<?php

/**
* Template Name: Usage
*/
    if(!current_user_can("manage_options")){
      echo "<h2>This page is for admins only</h2>";
    } else {
    $requestorID = 9;
    $requestorName = "MGH";
    
    // get credentials for mySQL server
    require_once ABSPATH.'/wp-content/themes/jomi/Counter/config.php';
    
    function getInstitutions(){
        global $db;
        // Create connection to our MySQL database
        $conn = new mysqli($db["host"], $db["user"], $db["pwd"], $db["db"]);

        // Check connection
        if ($conn->connect_error) {
            echo $conn->connect_error;
            return array();
        }
        
        // query the database for ip addresses
        $sqlQuery = "SELECT * FROM wp_institutions;";
        $result = $conn->query($sqlQuery);
        if($result == False){
            echo "Damn";
            return array();
            }
        
        // generate list of ip addresses from rows of query
        // the response will be like:
        // [ ["start" => _start of ip block_, "end" => _end of ip block_], ... ]
        
        $institutions = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($institutions, $row);
            }
        }
        return $institutions;
    }
    
    
?>
<script>
 var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var chart = false;
    
    // Store the COUNTER XML in xmlDoc in the type of an XMLDocument
    var xmlDoc;
    // Store the raw data in usage Data in a Dict with an array months holding
    // the list of months and an ar4ray visits holding the list of visits
    var usageData;
    
    var now = Date.now();
    
$(function(){
    $("#fetchDataButton").click(fetchData);
    
    datepickr.prototype.addClass = function (element, className) { element.className += ' ' + className; };
    
    datepickr('#beginDate', {maxDate:now});
    datepickr('#endDate', {maxDate:now});
    function createPlot(){
        
        document.getElementById("showWithGraph").className = "";
        var ctx = document.getElementById("myChart").getContext("2d");
        console.log(ctx);
        var data = {
            labels: usageData.months,
            datasets: [
                {
                    label: "Visits",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: usageData.visits
                },
                {
                    label: "Page Views",
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(151,187,205,1)",
                    data: usageData.actions
                }
            ]
        };
        var legend = document.getElementById("legend");
        legend.innerHTML = "";
        for(var i=0; i < data.datasets.length; i++){
            legend.innerHTML += '<span style="display:inline-block;width:.5em;height:.5em;border:1px solid #000' +
                                '; background-color: ' + data.datasets[i].strokeColor +';"></span> ' +
                                data.datasets[i].label +  ' &nbsp; &nbsp; &nbsp; ';
        }
        if(chart)
            chart.destroy();
        console.log(usageData);
        chart = new Chart(ctx).Line(data, {
            pointHitDetectionRadius : 3,
            legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\">" + 
                             "<% for (var i=0; i<datasets.length; i++){%><li>" +
                             "<span style=\"background-color:<%=datasets[i].strokeColor%>" +
                             "\"></span><%=datasets[i].label%></li><%}%></ul>"});
        

    }
    function parseUsageData(res){
    
        // trim removes leading/trailing whitespace that seems so common and the replace 
        // does a regex search for comments and removes them (the comments are added by wordpress)
        res = res.trim().replace(/<!--[\s\S]*?-->/g, "");
        
        xmlDoc = $.parseXML(res.trim());
        var items = xmlDoc.getElementsByTagName('ItemPerformance');
        if(items.length == 0){
          console.log(res);
        }
        
        /* Each ItemPerformance looks like this, by the way:
         *  <ItemPerformance>
         *      <Period>
         *          <Begin>$month[0]</Begin>
         *          <End>$month[1]</End>
         *      </Period>
         *      <Category>Requests</Category>
         *      <Instance>
         *          <MetricType>ft_total</MetricType>
         *          <Count>$month[2][1]</Count>
         *      </Instance>
         *      <Instance>
         *          <MetricType>other</MetricType>
         *          <Count>$month[2][0]</Count>
         *      </Instance>
         *  </ItemPerformance>
         * With "ft_total" describing total pageviews (should change in new site to describe
         * only article page views) and "other" describing total visitors
         */
         
        months = [];
        visits = [];
        actions = [];
        for(var i=0; i<items.length; i++){
            var date = new Date(items[i].children[0].children[1].textContent);
            console.log(date, date.getMonth());
            months.push(monthNames[date.getMonth()] + ' ' + date.getFullYear().toString().substr(2));
            actions.push(items[i].children[2].children[1].textContent);
            visits.push(items[i].children[3].children[1].textContent);
        }
        usageData = {months:months, visits:visits, actions:actions};
        console.log(actions[actions.length - 3]);
        createPlot();
    }
    function myDateString(date){
        return date.getFullYear() + '-' + (date.getUTCMonth() + 1) + '-' + date.getDate();
    }
    function generateRequest(dateRange){
        var selector = document.getElementById('institution');
        var id = selector.value;
        var name = selector.options[selector.selectedIndex].innerHTML;
        var identification = '<ID>' + id + '</ID><Name>' + name + '</Name>';
        return '<' + '?xml version="1.0" encoding="UTF-8"?><ReportRequest><Requestor>' + 
               identification + '</Requestor><CustomerReference>' + identification +
               '</CustomerReference><ReportDefinition Name="Jomi" Release="4"><Filters><UsageDateRange><Begin>' + 
               dateRange.beginDate + '</Begin><End>' + dateRange.endDate +
               '</End></UsageDateRange></Filters></ReportDefinition></ReportRequest>';
    }
    function fetchData(){
        dateRange = getDateRange();
        var request = generateRequest(dateRange);
        console.log(request);
        $.ajax({
            url: '/usage/counter',
            data: request, 
            type: 'POST',
            contentType: "text/xml",
            dataType: "text",
            success : parseUsageData,
            error : function (xhr, ajaxOptions, thrownError){  
                console.log(xhr.status);          
                console.log(thrownError);
            } 
        }); 
    }
    
    function getDateRange(){
        var beginDate = $('#start-year')[0].value + '-' + $('#start-month')[0].value +'-14';
        var endDate   =   $('#end-year')[0].value + '-' +   $('#end-month')[0].value +'-14';
        return {beginDate: beginDate, endDate: endDate};
    }


//         
    
    function download(filename, text,type) {
        var pom = document.createElement('a');
        pom.setAttribute('href', 'data:text/' + type + ';charset=utf-8,' + encodeURIComponent(text));
        pom.setAttribute('download', filename);

        pom.style.display = 'none';
        document.body.appendChild(pom);

        pom.click();

        document.body.removeChild(pom);
    }
    function downloadXML(){
        // xmlDoc is in the form of a JQuery XMLDocument. Apparently in IE there is the property xmlDoc.xml 
        // that contains the stringified version of the XML, but otherwise you have to use an XMLSerializer
        var xmlString = (window.ActiveXObject) ? xmlDox.xml :
                        (new XMLSerializer()).serializeToString(xmlDoc);
        download("JoMI_Usage_Statistics.xml", xmlString, "plain");
    }
    function downloadCSV(){
        var csvString = "Your Institution's JoMI Usage Data,\nMonth,Visitors,Page Views\n";
        for(var i=0; i<usageData.months.length; i++){
            csvString += usageData.months[i] + ", " + usageData.visits[i] + ", " + usageData.actions[i] + "\n";
        }
        download("JoMI_Usage_Statistics.csv", csvString, 'csv');
    }
});
        
   </script>
<h1>Usage Statistics</h1>
<p> Select your institution: 
<select id="institution">
    <?php
    $institutions = getInstitutions();
    var_dump($institutions);
    foreach($institutions as $institution){
        echo '<option value="'.$institution['id'].'">'.$institution['name'].'</option>\n    ';
    }
    ?>
</select></p>
<p> Select the date range over which you want your local usage statistics</p>
Start Date:
<select id="start-month">
  <option value='1'>January</option>
  <option value='2'>February</option>
  <option value='3'>March</option>
  <option value='4'>April</option>
  <option value='5'>May</option>
  <option value='6'>June</option>
  <option value='7' selected="selected">July</option>
  <option value='8'>August</option>
  <option value='9'>September</option>
  <option value='10'>October</option>
  <option value='11'>November</option>
  <option value='12'>December</option>
</select>
<select id="start-year">
  <option>2013</option>
  <option>2014</option>
  <option selected="selected">2015</option>
</select>
<br />
End Date:
<select id="end-month">
  <option value='1'>January</option>
  <option value='2'>February</option>
  <option value='3'>March</option>
  <option value='4'>April</option>
  <option value='5'>May</option>
  <option value='6'>June</option>
  <option value='7' selected="selected">July</option>
  <option value='8'>August</option>
  <option value='9'>September</option>
  <option value='10'>October</option>
  <option value='11'>November</option>
  <option value='12'>December</option>
</select>
<select id="end-year">
  <option>2013</option>
  <option>2014</option>
  <option selected="selected">2015</option>
</select>
<br /><br />
<button type="button" id="fetchDataButton"> Fetch Data </button><br/>
<div id="showWithGraph" class="hidden">
    <br/><br/>
    <h2>Visitors And Page Views Per Month</h2>
    <canvas id="myChart" width="600" height="400"></canvas>
    <div id="legend" style="text-align:center"></div><br /><br />
    <a href="#" onclick="downloadXML()">Download COUNTER Report as XML</a> &nbsp; &nbsp; &nbsp;
    <a href="#" onclick="downloadCSV()">Download CSV</a>
</div>

<?php } ?>
