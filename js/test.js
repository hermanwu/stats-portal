$(document).ready(function(){
		var agents = [
		              "Adam Coleman",
		              "Mark Rusinski",
		              "Nilesh Pawar",
		              "Herman Wu",
		              "Christopher Mccloskey",
		              "Gowtham Govindarajan",
		              "Sahithya Dinakar",
		              "Igor Obikhod",
		              "Abhishek Naik",
		              "Gabriele Griffin",
		              "Robert Thompson",
		              "Matthew Zaske",
		              "Naveen Pitchandi",
		              "Sara Stocks"
		              ];
		
		function nameTransferToEmailPrefix(agentNames){
			var emailPrefixs = [];
			for(i = 0; i < agentNames.length; i++){
				var emailPrefix = agentNames[i].split(' ').join('');
				emailPrefixs.push(emailPrefix);
			}
			return emailPrefixs;
		}
		//nameTransferToEmailPrefix(agents);
		
		
		//create statistics table
		function getTicketStats(nameArray){
			var monday = "2014-09-01"
			$("#since").append(monday);
			$("#lastUpdate").empty();			
			$("#lastUpdate").append(new Date());
			for(i=0;i<nameArray.length;i++){
				getWeeklySolvedCount(nameArray[i], monday);
				getActiveTicket(nameArray[i]);
			}
		}
		
		
		function getWeeklySolvedCount(email, monday){
				$.post("script.php",{postEmail:email, status: "solved", monday:monday},function(data){
					var obj = JSON.parse(data);	
					$("#" + email + "> .weeklySolved").append(obj.results.length);
					
					var temp = $("#weeklySolved").text();
					$("#weeklySolved").text(+temp + +obj.results.length);
				});
		}
		
		function getActiveTicket(email){
			$.post("script.php",{postEmail: email, status: "open"},function(data){
				var obj = JSON.parse(data);	
				//alert (data);
				var weekArray = ticketSummary(obj, email);
				var openTicketNum =obj.results.length;
				$("#" + email + "> .totalTicket").append(openTicketNum);
				
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
			});
		}
		
		
		//create table header 
		function createTable(nameArray, agentNames){
			for(i=0;i<nameArray.length;i++){
				$("#statsTable").append(
				"<tr id=\"" + 
				nameArray[i] + 
				"\">" +
				"<td class = \"name\">"+
				"<a href=\"#\" id=\"showDetail_" + i + "\">" + agentNames[i] + "</a>" +
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
				"</tr>"+
			    "<tr >" +
			    "<td id=\"extra_" + i + "\" style=\"display:none;\" colspan=\"11\">" +
			    "<div id=\"chart_div\">Response Time Bar Chart" +
			    "</div>" +
			    "</td>" +
			    "</tr>"
				);

			}
			
			$("a[id^=showDetail_]").click(function(event) {
			   $("#extra_" + $(this).attr('id').substr(11)).toggle();
			   event.preventDefault();
			});
			
			//append summary of the ticket
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
			

			for(i=2; i<10; i++)
			{
				$("#statsTable tr").eq(i).css('background-color','#FAFAD2');
			}
			
			//set color for web team
			for(i=10; i<24; i++)
			{
				$("#statsTable tr").eq(i).css('background-color','#F0F8FF');
			}
			
			for(i=24; i<30; i++)
			{
				$("#statsTable tr").eq(i).css('background-color','#C9F8FF');
			}
			
		}

				
		function agentScore(weekArray){
			var score = weekArray[0].length * 0
			          + weekArray[1].length * 1
			          + weekArray[2].length * 2
			          + weekArray[3].length * 6
			          + weekArray[4].length * 10
			          + weekArray[5].length * 15;
			return score;
		}

		function ticketSummary(object, email){
			var weekArray = [[],[],[],[],[],[]];
			//weekArray[0].push("11111");
			//alert(weekArray[0][0]);
			var i;
			var week;
			for (i=0; i<object.results.length; i++){
				//alert (object.results[i].created_at);
				week = calculateWeek(object.results[i].created_at);
				index = Math.floor(week);
				//check whehter user has ticket more than 6 weeks old
				if (index > 5){
					$('<a>',{
					    text: email + ' has a ' + (index+1) + " week old ticket: " + object.results[i].id,
					    href:"https://airwatch.zendesk.com/agent/#/tickets/" + object.results[i].id,
					    target: "_blank"
					}).appendTo('#extra');
					$("#extra").append("</br>");
				}
				else{
				weekArray[index].push(object.results[i].id);
				}
			}
			return weekArray;
		}
		
		
		//create ticket box's string 
		function ticketBox(ticketArray){
			ticketBoxString = ""
			for(i = 0; i < ticketArray.length; i++){
				ticketBoxString = ticketBoxString + "<a href=\"https://airwatch.zendesk.com/agent/#/tickets/" + ticketArray[i] +"\" target=\"_blank\">" + ticketArray[i] + "</a></br>";
			}
			return ticketBoxString;
		
		//calculate the age of the ticket solved after start of the week;
		function calculateWeek(timeString){
			var createdTime = new Date(timeString);
			var curTime = new Date();
			var week = (curTime - createdTime) / (1000 * 60 * 60 * 24 * 7);
			return week;
		}
		
		
	   function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['UpdateTime', '1 day', '2 days','3+ days'],
		          ['# of tickets',  4,  3, 1]
		        ]);

		        var options = {
		            title: 'Last Update',
		            isStacked: true,
		            legend: { position: 'right'},
		            width: 900,
		            height:90
		        };

		        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));

		        chart.draw(data, options);
	   }


		//functions to run
		var emailPrefixs = nameTransferToEmailPrefix(agents);
		createTable(emailPrefixs, agents);
		getTicketStats(emailPrefixs);
		$("#extra").fadeIn("slow");
		//draw google bar chart
		//google.setOnLoadCallback(drawChart);

});