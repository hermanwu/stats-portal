
$(document).ready(function(){

// VARIABLE
		//$(".active > a").text("Enhanced");
		var activeTab = "Enhanced";
		//setup local storage
		if (typeof(Storage) != "undefined") {
			if(localStorage.getItem("currentTab")){
				activeTab = localStorage.getItem("currentTab");
				setTab(activeTab);
			}
			else{
				localStorage.setItem("currentTab", activeTab);
			}
		}
		
		$("#teamDropDownBox").change(function(){
			//clearTable();
			var selectedTeam = $(this).val();
			localStorage.setItem("currentTab", selectedTeam);
			teamId = getTeamId(selectedTeam);
			location.reload();
			//loadTable(teamId);
		});
		
		var teamId = getTeamId(activeTab);
		var phoneTeam = [];	
		var webTeam = [];
		var totaloneday = 0;
		var totaltwoday = 0;
		var totalplusday = 0;
		
		
// HELP FUNCTIONS
			
		function getTeamId(activeTab){
			if(activeTab === "Enhanced"){
				return 21232998;
			}
			else if (activeTab === "Basic"){
				return 21550038;
			}
			else if (activeTab === "Enterprise"){
				return 22222246;
			}
			else if (activeTab === "T3"){
				return 20823347;
			}
		}
		
		
		function setTab(teamName){
			document.getElementById(teamName).selected = "true";
		}
		
		function clearTable(){
			$("#total").remove();
			$(".statsRows").remove();
			$(".extra").remove();
			$("#piechart").empty();
			totaloneday = 0;
			totaltwoday = 0;
			totalplusday = 0;
		}
		//function to convert name to prefix
		function nameTransferToEmailPrefix(agentNames){
			var emailPrefixs = [];
			for(var i = 0; i < agentNames.length; ++i){
				var emailPrefix = agentNames[i].split(' ').join('');
				emailPrefixs.push(emailPrefix);
			}
			return emailPrefixs;
		}
	
		//function to calculate Monday of this week
		function calculateMonday(){
			var todayDate = new Date();
			var todayDay = todayDate.getDay();
			var indexWeekArray = [6,0,1,2,3,4,5];
			var mondayDate = todayDate - indexWeekArray[todayDay]*1000*60*60*24;
			var mondayTimeToPrint = new Date(mondayDate);
			return mondayTimeToPrint.getFullYear()+"-"+(mondayTimeToPrint.getMonth()+1)+"-"+mondayTimeToPrint.getDate();
		}
		
		//calculate the age of the ticket solved after start of the week;
		function calculateWeek(timeString){
			var createdTime = new Date(timeString);
			var curTime = new Date();
			var week = (curTime - createdTime) / (1000 * 60 * 60 * 24 * 7);
			return week;
		}
		
		function calculateResponseTime(updatedTime, monday, numOfAgedTicket){
			curTime = new Date();	
			day = (curTime - updatedTime) / (1000*60*60*24);
			
			if(updatedTime < monday){
				day = day - 2;
			}
			if (day >= 2){
				numOfAgedTicket[2]++;
			}
			else if (day >= 1){
				numOfAgedTicket[1]++;
			}
			else{
				numOfAgedTicket[0]++;				
			}	
		}
		
		function simplifyName(fullName){
			 return fullName.substr(0, fullName.indexOf(' ')+2)+".";			
		}
		
// DATA PROCESSING
		//get Data from Zendesk and put them into table
		function getLastUpdatedTime(){
			//$("#lastUpdatedTime").append("test");
			$.post("php/action.php",{action: 'selectSchedule', input1: teamId}, function(data){
				$("#lastUpdatedTime").append(data);
			});
		}
		
		function getTicketStats(agentIdArray, nameArray, emailArray, teamType){
			var monday = calculateMonday();
			for(var i=0;i<agentIdArray.length;i++){
				getWeeklySolvedCount(agentIdArray[i], emailArray[i], monday);
				getActiveTicket(agentIdArray[i], emailArray[i], teamType);
			}
		}
		
		function getWeeklySolvedCount(emailPrefix, emailAddress, monday){
				var getWeeklySolvedQuery = "/search.json?query=solved>"+monday+"+assignee:"+emailAddress+"+type:ticket+status>pending";
				$.post("php/APICall.php", {searchQuery: getWeeklySolvedQuery, queryName: emailAddress+"Solved"}, function(data){
					var obj = JSON.parse(data);	
					$("#" + emailPrefix + "> .weeklySolved").append(obj.results.length);
					
					var temp = $("#weeklySolved").text();
					$("#weeklySolved").text(+temp + +obj.results.length);
				});
		}
		
		function getActiveTicket(email, emailAddress, teamType){
			$.post("php/action.php",{action:'getActiveTicket',input1:email},function(data){
				var weekArray = [[],[],[],[],[],[]];
				var totalArray = [];
				var i;
				var week;
				
				var obj = JSON.parse(data);
				//alert(obj[0]['createdDate']);
				for(i=0; i<obj.length; i++){
					week = calculateWeek(obj[i]['createdDate']);
					//alert(week);
					index = Math.floor(week);
					//totalArray.push(obj[i]['ticketID']+" "+)
					var tempObj = {'ticketID': obj[i]['ticketID'], 'updatedDate': obj[i]['updatedDate']};
					totalArray.push(tempObj);
					if (index > 5){
						var oldTicketUrl = "\"https://airwatch.zendesk.com/agent/#/tickets/" + obj[i]['ticketID'] + "\"";
						$("#extra").append("<a href="+oldTicketUrl+" target=\"_blank\">"+ obj[i]['ticketID'] + ": " + emailAddress + " --"+(index+1)+" weeks old"+ "<\a>");
						$("#extra").append("</br>");
					}
					else{
						//alert(obj[i]['ticketID']);
						weekArray[index].push(tempObj);
					}
				}	
				
				//alert(weekArray);
				var openTicketNum =obj.length;
				$("#" + email + "> .totalTicket").append("<div class=\"tiptext\">" + openTicketNum + "<div class=\"description\">" + ticketBox(totalArray) + "</div>" + "</div>");
				
				var temp2 = $("#totalTicket").text();
				$("#totalTicket").text(+temp2 + +openTicketNum);
				
				$("#" + email + "> .1week").append("<div class=\"tiptext\">" + weekArray[0].length + "<div class=\"description\">" + ticketBox(weekArray[0]) + "</div>" + "</div>");
				var week1 = $("#week1").text();
				$("#week1").text(+week1 + +weekArray[0].length);
				
				$("#" + email + "> .2week").append("<div class=\"tiptext\">" + weekArray[1].length + "<div class=\"description\">" + ticketBox(weekArray[1]) + "</div>" + "</div>");
				var week2 = $("#week2").text();
				$("#week2").text(+week2 + +weekArray[1].length);
				
				$("#" + email + "> .3week").append("<div class=\"tiptext\">" + weekArray[2].length + "<div class=\"description\">" + ticketBox(weekArray[2]) + "</div>" + "</div>");
				var week3 = $("#week3").text();
				$("#week3").text(+week3 + +weekArray[2].length);
				
				$("#" + email + "> .4week").append("<div class=\"tiptext\">" + weekArray[3].length + "<div class=\"description\">" + ticketBox(weekArray[3]) + "</div>" + "</div>");
				var week4 = $("#week4").text();
				$("#week4").text(+week4 + +weekArray[3].length);
				
				$("#" + email + "> .5week").append("<div class=\"tiptext\">" + weekArray[4].length + "<div class=\"description\">" + ticketBox(weekArray[4]) + "</div>" + "</div>");
				var week5 = $("#week5").text();
				$("#week5").text(+week5 + +weekArray[4].length);
				
				$("#" + email + "> .6week").append("<div class=\"tiptext\">" + weekArray[5].length + "<div class=\"description\">" + ticketBox(weekArray[5]) + "</div>" + "</div>");
				var week6 = $("#week6").text();
				$("#week6").text(+week6 + +weekArray[5].length);
				
				$("#" + email + "> .totalScore").append(agentScore(weekArray));
				$("#" + email + "> .weightedScore").append((agentScore(weekArray) / openTicketNum).toFixed(2));						
				$(".tiptext").mouseover(function() {
				    $(this).children(".description").show();
				}).mouseout(function() {
				    $(this).children(".description").hide();
				});
				drawChart(email, emailAddress, teamType);
			});
		}
		
		//use formula given by David
		function agentScore(weekArray){
			var score = weekArray[0].length * 0
			          + weekArray[1].length * 1
			          + weekArray[2].length * 2
			          + weekArray[3].length * 6
			          + weekArray[4].length * 10
			          + weekArray[5].length * 15;
			return score;
		}
		
		
		//generate pie chart data
		function generatePieChartData(){
			  var title = ['Last Public Comment Response', '# of tickets'];
			  var pie1 = ['<24hr', totaloneday];
			  var pie2 = ['<48hr', totaltwoday];
			  var pie3 = ['>48hr', totalplusday];
			  var pieChartData = [title, pie1, pie2, pie3];
			  return pieChartData;
		} 
		
		 
// TABLES AND GRAPHS	
		
		//create table header 
		function createTable(agentIdArray, agentNames, teamName){
			//alert(agentIdArray.length);
			for(var i=0;i<agentIdArray.length; i++){
				$("#statsTable").append(
				"<tr id=\"" + 
				agentIdArray[i] + 
				"\" class = \"" + 
				teamName + " statsRows\">" +
				"<td class = \"name\">"+
				"<a href=\"#\" id=\"showDetail_" + agentIdArray[i] + "\" style=\"margin-left:20px\">" + agentNames[i] + "</a>" +
				"<a href=\"#\" id=\"deleteUser_" + agentIdArray[i] + "\" class=\"btn-mini deleteUser\" style=\"float:left\"><i class=\"icon-minus-sign\"></i></a>"+
				"</td>" +
				"<td class = \"weeklySolved\"></td>" +
				"<td class = \"totalTicket\"></td>" +
				"<td class = \"1week\"></td>" +
				"<td class = \"2week\"></td>" +
				"<td class = \"3week\"></td>" +
				"<td class = \"4week\"></td>" +
				"<td class = \"5week\"></td>" +
				"<td class = \"6week\"></td>" +
				"<td class = \"totalScore\"></td>" +
				"<td class = \"weightedScore\"></td>" +
				"</tr>" +
			    "<tr class=\"" +
			    teamName + " statsRows\">"+
			    "<td id=\"extra_" + agentIdArray[i] + "\" style=\"display:none;\" colspan=\"11\">" +
			    "<div id=\"chart_div_" + agentIdArray[i] + "\">LOADING......." +
			    "</div>" +
			    "</td>" +
			    "</tr>"
				);
			}
		}
		
		
		//create ticket box's string 
		function ticketBox(ticketArray){
			ticketBoxString="";
			for(var i = 0; i < ticketArray.length; i++){
				ticketBoxString = ticketBoxString + "<a href=\"https://airwatch.zendesk.com/agent/#/tickets/" + ticketArray[i]['ticketID'] +"\" target=\"_blank\">" 
								+ ticketArray[i]['updatedDate'] 
								+ ": " 
								+ ticketArray[i]['ticketID'] 
								+ "</a></br>";
			}
			return ticketBoxString;
		}	   
		
	   //add clickable button to every row
	   function addDropButton(){
			$("a[id^=showDetail_]").click(function(event) {
			   $("#extra_" + $(this).attr('id').substr(11)).toggle();
			   event.preventDefault();
			});
			$("a[id^=deleteUser_]").click(function(event) {
				   var agentId = $(this).attr('id').substr(11);
				   var agentName = $("#showDetail_"+agentId).text(); 
				   var confirmationSentenceToDisplay = "Are you sure to remove agent: "+agentName+" from the table?";
				   var confirmationPromptResponse = confirm(confirmationSentenceToDisplay);
				   if (confirmationPromptResponse){
					   $.post("php/PDOdelete.php", {agentId: agentId}, function(data){
						   if(data != 1){
							   alert(data);
						   }
						   else{
							   location.reload();
						   }
					   });
					   
				   };
			});
	   }
	   
	   function appendStatsSum(){
			$("#statsTable").append(
					"<tr id=\"" + 
					"total" + 
					"\">" +
					"<td class = \"total\">"+
					"Total" +
					"</td>" +
					"<td id = \"weeklySolved\">0</td>" +
					"<td id = \"totalTicket\">0</td>" +	
					"<td id = \"week1\">0</td>" +	
					"<td id = \"week2\">0</td>" +	
					"<td id = \"week3\">0</td>" +	
					"<td id = \"week4\">0</td>" +	
					"<td id = \"week5\">0</td>" +	
					"<td id = \"week6\">0</td>" +	
					"</tr>"
					);
	   }
	   
	   //append Monday to the table
	   function appendMonday(){
		   var monday = calculateMonday();
		   $(".sinceCell").append(monday);
		   
	   }
	   

		// draw pie chart 
		function drawPieChart(inputDateForPieChart) {
		        var data = google.visualization.arrayToDataTable(inputDateForPieChart);
		        var options = {
		          title: 'Total Ticket Count based on Responese Time',
		          colors: ['#A9F5A9', '#F4FA58', '#FA5858'],
		          chartArea: { width: "80%", height: "80%" },
		          pieSliceTextStyle: {color:'black', fontSize: 15},
		          titleTextStyle: {fontSize:15}
		        };
		        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
		        chart.draw(data, options);
		}
		
		
		
	   function drawChart(agentId, emailAddress, teamType) {
		   $.post("php/action.php", {action:'retrieveActiveTicketArray',input1:agentId}, function(data){
			    var updatedTime;
				var numOfAgedTicket = [0,0,0];
				var obj = JSON.parse(data);
				var monday = new Date(calculateMonday());
				for(var i = 1; i<obj.length; i+=2){
					updatedTime = new Date(obj[i]);
					calculateResponseTime(updatedTime, monday, numOfAgedTicket);
				}
		   		var chartDiv = "chart_div_" + agentId;
		   		var titleArray = ['UpdateTime', '< 24 hrs', '< 48 hrs','> 48 hrs'];
		   		var hoverExplaination = ['# of tickets'];
		   		//var numberOfAgedTicket = numOfAgedTicket;
		   		var ticketArray = hoverExplaination.concat(numOfAgedTicket);
		   		//totaloneday = totaloneday+numOfAgedTicket[0];
		   		var tableArray = [];
		   		tableArray[0]=titleArray;
		   		tableArray[1]=ticketArray;
		        var data = google.visualization.arrayToDataTable(tableArray);
		        
		        var options = {
		            title: 'Last Public Comments Updated by Agent',
		            isStacked: true,
		            colors: ['#A9F5A9', '#F4FA58', '#FA5858'],
		            width: 800,
		            height:90,
		            chartArea: {width: '70%'},
		            hAxis: {maxValue: 30}
		        };
		        var chart = new google.visualization.BarChart(document.getElementById(chartDiv));
		        chart.draw(data, options);
		        if(teamType == 0 |teamType == 1){
		        	totaloneday += numOfAgedTicket[0];
		    		totaltwoday += numOfAgedTicket[1];
		    		totalplusday += numOfAgedTicket[2];
		        }
			});
	   }

// Event Listeners
	  
	   $( ".detailButton" ).click(function() {
		      $(".detailButton").text(function(i, text){
		    	  if(text ==="show details"){
		    		  
		    		  $("td[id^=extra_]").show();
		    		  return "hide details";
		    	  }
		    	  else{
		    		  $("td[id^=extra_]").hide();
		    		  return "show details";
		    	  }
		      });
		});

	   
	   $("#addMemberButton").click(function(){
	
		   var categories = document.getElementById( "primaryCategory" );
		   var selectedCategory = categories.selectedIndex+1;
		   var agentEmail = $("#addAgentEmail").val();
		   
		   $.post("php/PDOaddAgent.php", {agentEmail: agentEmail, category:selectedCategory, teamId:teamId}, function(data){
			   if(data != 1){
				   alert(data);
			   }
			   else{
				   location.reload();
			   }
		   });
	   });
	   
	   
	   $(document).scroll(function () {
		    var y = $(this).scrollTop();
		    if (y > $("#statsTable").offset().top) {
		        $('#header-fixed').show();
		    } else {
		        $('#header-fixed').hide();
		    }
		});
	   
	   
	  // draw pie chart after all ajax functions are finished
	  $(document).ajaxStop(function () {
		  setTimeout(function(){
			  drawPieChart(generatePieChartData());}, 500);
	  });
	  
	   
	  function loadTable(teamIdToLoad){
		 
		   var allAgentArrayName = [phoneTeam, webTeam];
		   var colors = ["phone","web"];
		    
		
		    // json call to get all agents information 
		    $.post("php/SQLselect.php", {teamId: teamIdToLoad}, function(data){
		    	
		    	var returnArray = JSON.parse(data);
		    	var allAgentArray = [];
		    	allAgentArray[0]=returnArray[0];
		    	allAgentArray[1]=returnArray[1];
		    	
		    	var allEmailArray = [];
		    	allEmailArray[0] = returnArray[2];
		    	allEmailArray[1] = returnArray[3];
		    	
		    	var allAgentIdArray = [];
		    	allAgentIdArray[0] = returnArray[4]; 
		    	allAgentIdArray[1] = returnArray[5];
		    	
		    	for(var allAgentArrayIndex = 0; allAgentArrayIndex < allAgentArrayName.length; allAgentArrayIndex++){
		    		var team = allAgentArray[allAgentArrayIndex];
		    		
		    		var emailPrefixs = nameTransferToEmailPrefix(team);
		    		
		    		var agentIdArray = allAgentIdArray[allAgentArrayIndex];
		    		var emailAddressArray = allEmailArray[allAgentArrayIndex]; 
		    		
		    		createTable(agentIdArray, team, colors[allAgentArrayIndex]);
		    		getTicketStats(agentIdArray, emailPrefixs, emailAddressArray, allAgentArrayIndex);
		    		$("#extra").fadeIn("slow");
		    	}
		    	
		    addDropButton();
			appendStatsSum();
		    });
		    $.post("php/storeTicket.php",{postTeam: teamIdToLoad});
	  }
	   
	   //Main methods to run
	  appendMonday();
	  getLastUpdatedTime();
	  loadTable(teamId);
	  //store all tickets that have not been stored in database 
	  
});
