<?php
	
	switch(isset($_GET['function'])? $_GET['function'] : ""){
	case 'delete' : del($_GET['no']); break;
	case 'new' : add($_GET['name'], $_GET['surname'], $_GET['department']); break;
	case 'update': update($_GET['no'], $_GET['name'], $_GET['surname'], $_GET['department']); break;
	case 'get': getStudents(); break;
	default: echo pageHeader("Student List");
	}

	function pageHeader($title){ ?>
		<!DOCTYPE html>
		<html lang="en">
			<head>
				<title><?php echo $title; ?></title>
				<meta charset="UTF-8">
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
				<style type="text/css">
					.center{
						text-align: center;
					}
					.round-button{
						width: 40px;
						height: 40px;
						margin: 5px 5px;
						padding: 7px 10px;
						border-radius: 20px;
						font-size: 15px;
						text-align: center;
					}
					table.table-striped > .thead-dark > tr > th:hover{
						color: #AABBCC;
						cursor: pointer;
					}
				</style>
				<script>
					/*var students;
					updateRequest = new XMLHttpRequest();
					updateRequest.open("GET", "StudentList.php?function=get", true);
					updateRequest.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							students = this.responseText;
							console.log(this.responseText);
						}
					};
					updateRequest.send();*/
					var students = <?php echo getStudents(); ?>;
					var nextNumber = <?php echo getNextNumber(); ?>;
					var findList = [], find = false;
					const ENTRY_PER_PAGE = 5;
					
					function comp(a, b){
						if(a > b) return 1;
						if(b > a) return -1;
						return 0;
					}
					function sortList(list){
						list.sort(function(a, b){
								var reverse = 1;
								if(!ascending) reverse = -1;
								
								switch(column){
									case "no": return (a.no - b.no) * reverse;
									case "name": return (comp(a.name, b.name)) * reverse;
									case "surname": return (comp(a.surname, b.surname)) * reverse;
									case "department": return (comp(a.department, b.department)) * reverse;
								}
								
						});
					}
					
					function showList(page, column, ascending, find){
						var mTable = document.getElementById("studentList");
						var headerRow = mTable.rows[0].cells;

						switch(window.column){
							case "no": headerRow[0].innerHTML = "Student Number"; break;
							case "name": headerRow[1].innerHTML = "Name"; break;
							case "surname": headerRow[2].innerHTML = "Surname"; break;
							case "department": headerRow[3].innerHTML = "Department"; break;
						}
						
						if(page != window.page) 
							window.page = page;
						else if(column != window.column || ascending != window.ascending){
							
							if(column != window.column){
								window.column = column;
								window.ascending = ascending = true;
							}
							else if(ascending != window.ascending)
								window.ascending = ascending;
							
							if(find)
								sortList(findList);
								
							else
								sortList(window.students);
								
						}

						if(find != window.find){
							window.find = find;

							if(find)
								sortList(findList);
							else
								sortList(window.students);
						}
						
						var students = window.students;
						if(find)
							students = findList;
						
						var updown = (ascending)? "&nbsp;&nbsp;&nbsp;&nbsp;&#x25b2;" : "&nbsp;&nbsp;&nbsp;&nbsp;&#x25bc;";
							
						switch(column){
							case "no": headerRow[0].innerHTML += updown; break;
							case "name": headerRow[1].innerHTML += updown; break;
							case "surname": headerRow[2].innerHTML += updown; break;
							case "department": headerRow[3].innerHTML += updown; break;
						}
					
						var length = mTable.rows.length, c;
						
						for(c = length - 1; c > 2; c--)
							mTable.deleteRow(c);
						
						students.forEach(function(student, index) {
								if(index >= (page - 1) * 5 && index < page * 5)
									addNewRow(mTable, -1, student);
						});
						
						putPageButtons(students.length);
						
					}
					function putPageButtons(length){
						var pageButton, count, pageCount, div = document.getElementById("pageButtons");
						while(div.firstChild)
							div.removeChild(div.firstChild);
						
						if(length == 0) pageCount = 1;
						else
							pageCount = (length % ENTRY_PER_PAGE == 0)? length / ENTRY_PER_PAGE : parseInt(length / ENTRY_PER_PAGE) + 1;
						
						if(page != 1){
							makePageButton(div, "&lt;&lt;", 1);
							makePageButton(div, "&lt;", page - 1);
						}
						for(count = 1; count <= pageCount; count++){
							if(count == page){
								makePageButton(div, count);
								continue;
							}
							var c = count;
							makePageButton(div, c, c);
						}
						if(page != pageCount){
							makePageButton(div, "&gt;", page + 1);
							makePageButton(div, "&gt;&gt;", pageCount);
						}
					}
					function makePageButton(div, name, page){
						var pageButton = document.createElement("button");
						pageButton.type = "button";
						if(page === undefined)
							pageButton.className = "btn btn-primary round-button disabled";
						else{
							pageButton.className = "btn btn-primary round-button";
							pageButton.onclick = function(){showList(page, column, ascending, find)};
						}
						pageButton.innerHTML = name;
						div.append(pageButton);
					}
					function arrange(header, headerRow){
						var cells = headerRow.cells, column;
						for(var i = 0; i < 4; i++)
							cells[i].style.color = "";

						switch(header.id){
							case "hNo" : column = "no"; break;
							case "hName" : column = "name"; break;
							case "hSurname" : column = "surname"; break;
							case "hDepartment" : column = "department"; break;
						}

						header.style.color = "#8888ff";
						showList(page, column, !ascending, find);
					}
					
					function findStudent(){
						var findNo = document.getElementById("findNo").value;
						var findName = document.getElementById("findName").value;
						var findSurname = document.getElementById("findSurname").value;
						var findDepartment = document.getElementById("findDepartment").value;
						
						findList = [];
						if(findNo == "" && findName == "" && findSurname == "" && findDepartment == ""){
							window.alert("No information has been entered");
							return ;
						}
						for(var i = 0; i < students.length; i++){	
							if((findNo == students[i].no || findNo == "") 
									&& (findName == students[i].name || findName == "") 
									&& (findSurname == students[i].surname || findSurname == "") 
									&& (findDepartment == students[i].department || findDepartment == ""))
								findList.push(students[i]);
						}
						if(!findList.length)
							window.alert("No matches found");
						else
							showList(1, column, ascending, true);
						
					}
					function changeRow(row){
						var name = row.cells[1].innerHTML;
						var surname = row.cells[2].innerHTML;
						var department = row.cells[3].innerHTML;
						
						row.deleteCell(1);
						var cName = row.insertCell(1);
						row.deleteCell(2);
						var cSurname = row.insertCell(2);
						row.deleteCell(3);
						var cDepartment = row.insertCell(3);
						
						var tName = document.createElement("input");
						tName.type = "text";
						tName.id = "uName";
						tName.value = name;
						cName.appendChild(tName);
						
						var tSurname = document.createElement("input");
						tSurname.type = "text";
						tSurname.id = "uSurname";
						tSurname.value = surname;
						cSurname.appendChild(tSurname);
						
						var tDepartment = document.createElement("input");
						tDepartment.type = "text";
						tDepartment.id = "uDepartment";
						tDepartment.value = department;
						cDepartment.appendChild(tDepartment);
						
						var bSave = row.cells[4].firstElementChild;
						bSave.innerHTML = "Save";
						bSave.onclick = function(){update(row.rowIndex, name, surname, department)};

						var bCancel = row.cells[5].firstElementChild;
						bCancel.innerHTML = "Cancel";
						bCancel.onclick = function(){changeBackRow(row, name, surname, department)};
					}
					function changeBackRow(row, name, surname, department){
						row.deleteCell(1);
						var cName = row.insertCell(1);
						row.deleteCell(2);
						var cSurname = row.insertCell(2);
						row.deleteCell(3);
						var cDepartment = row.insertCell(3);
						
						cName.innerHTML = name;
						cSurname.innerHTML = surname;
						cDepartment.innerHTML = department;

						var cUpdate = row.cells[4].firstElementChild;
						cUpdate.innerHTML = "Update";
						cUpdate.onclick = function(){changeRow(row)};

						var cDelete = row.cells[5].firstElementChild;
						cDelete.innerHTML = "Delete";
						cDelete.onclick = function(){del(row.rowIndex)};
					}
					function update(index, name, surname, department){
						var mTable = document.getElementById("studentList");
						var row = mTable.rows[index];

						var no = row.cells[0].innerHTML;
						var nName = row.cells[1].firstElementChild.value;
						var nSurname = row.cells[2].firstElementChild.value;
						var nDepartment = row.cells[3].firstElementChild.value;
						
						if(nName == name && nSurname == surname && nDepartment == department){
							changeBackRow(row, name, surname, department);
							window.alert("No changes have been made");
							return ;
						}
						if(nName == "" && nSurname == "" && nDepartment == ""){
							changeBackRow(row, name, surname, department);
							return ;
						}

						changeBackRow(row, nName, nSurname, nDepartment);
						
						for(var i = 0; i < students.length; i++)
							if(students[i].no == no){
								if(nName != "" && nName != name) students[i].name = nName;
								if(nSurname != "" && nSurname != surname) students[i].surname = nSurname;
								if(nDepartment != "" && nDepartment != department) students[i].department = nDepartment;
							}
						
						if(find){
							if(nName != "" && nName != name) findList[(page - 1) * ENTRY_PER_PAGE + index - 3].name = nName;
							if(nSurname != "" && nSurname != surname) findList[(page - 1) * ENTRY_PER_PAGE + index - 3].surname = nSurname;
							if(nDepartment != "" && nDepartment != department) findList[(page - 1) * ENTRY_PER_PAGE + index - 3].department = nDepartment;
						}

						var newIndex, student;
						if(column != "no"){
							if(find){
								sortList(findList);
								for(var i = 0; i < findList.length; i++)
									if(findList[i].no == no){
										newIndex = i;
										student = findList[i];
									}
							}
							else{
								sortList(students);
								for(var i = 0; i < students.length; i++)
									if(students[i].no == no){
										newIndex = i;
										student = students[i];
									}
							}
							if(newIndex >= (page - 1) * ENTRY_PER_PAGE && newIndex < page * ENTRY_PER_PAGE){
								var rowIndex = newIndex % ENTRY_PER_PAGE + 3;
								mTable.deleteRow(index);
								addNewRow(mTable, rowIndex, student);
							}
						}

						updateRequest = new XMLHttpRequest();
						updateRequest.open("GET", "StudentList.php?function=update&no=" + no + "&name=" + nName + "&surname=" + nSurname + "&department=" + nDepartment, true);
						updateRequest.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								console.log(this.responseText);
								<?php echo "this.responseText"; ?>;
							}
						};
						updateRequest.send();
						
					}
					function del(index){
						var mTable = document.getElementById("studentList");
						var no = mTable.rows[index].cells[0].innerHTML;
						mTable.deleteRow(index);
						
						var student, length = (find)? findList.length : students.length;
						
						if(mTable.rows.length == 7 && length > page * ENTRY_PER_PAGE){
							if(find)
								student = findList[page * ENTRY_PER_PAGE];
							else
								student = students[page * ENTRY_PER_PAGE];
							
							addNewRow(mTable, -1, student);
						}
						else if(mTable.rows.length == 3)
							showList(page - 1, column, ascending, find);
						
						if(find){
							findList.splice((page - 1) * ENTRY_PER_PAGE + index - 3, 1);

							for(var i = 0; i < students.length; i++)
								if(students[i].no == no)
									students.splice(i, 1);

							if(findList.length % ENTRY_PER_PAGE == 0)	
								putPageButtons(findList.length);
						}
						else{
							students.splice((page - 1) * ENTRY_PER_PAGE + index - 3, 1);
							if(students.length % ENTRY_PER_PAGE == 0)
								putPageButtons(students.length);
						}
						
						updateRequest = new XMLHttpRequest();
						updateRequest.open("GET", "StudentList.php?function=delete&no=" + no, true);
						
						updateRequest.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								<?php echo "this.responseText"; ?>;
							}
						};
						updateRequest.send();
					}
					function newStudent(){
						var newNo = document.getElementById("newNo");
						var newName = document.getElementById("newName");
						var newSurname = document.getElementById("newSurname");
						var newDepartment = document.getElementById("newDepartment");
						
						if(newName.value == "" && newSurname.value == "" && newDepartment.value == ""){
							window.alert("Please enter information");
							return ;
						}
						
						var no = newNo.innerHTML, name = newName.value, surname = newSurname.value, department = newDepartment.value;
						
						var mTable = document.getElementById("studentList");

						students.push({"no":no,"name":name,"surname":surname,"department":department});
						nextNumber++;

						if(find){
							var findNo = document.getElementById("findNo").value;
							var findName = document.getElementById("findName").value;
							var findSurname = document.getElementById("findSurname").value;
							var findDepartment = document.getElementById("findDepartment").value;

							if((findNo == no || findNo == "") 
									&& (findName == name || findName == "") 
									&& (findSurname == surname || findSurname == "") 
									&& (findDepartment == department || findDepartment == ""))
								findList.push(students[students.length - 1]);
						}
						var index, student = students[students.length - 1];
						if(column != "no"){
							if(find){
								sortList(findList);
								for(var i = 0; i < findList.length; i++)
									if(findList[i].no == no)
										index = i;
							}
							else{
								sortList(students);
								for(var i = 0; i < students.length; i++)
									if(students[i].no == no)
										index = i;
							}
							if(index >= (page - 1) * ENTRY_PER_PAGE && index < page * ENTRY_PER_PAGE){
								var rowIndex = index % ENTRY_PER_PAGE + 3;
								addNewRow(mTable, rowIndex, student);
								if(mTable.rows.length > 8)
									mTable.deleteRow(-1);
							}
						}
						
						newNo.innerHTML = nextNumber;
						newName.value = "";
						newSurname.value = "";
						newDepartment.value = "";
						
						if(find && findList.length % ENTRY_PER_PAGE == 1)
							putPageButtons(findList.length);
						else if(students.length % ENTRY_PER_PAGE == 1)
							putPageButtons(students.length);
						
						updateRequest = new XMLHttpRequest();
						updateRequest.open("GET", "StudentList.php?function=new&name=" + name + "&surname=" + surname + "&department=" + department, true);
						
						updateRequest.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								<?php echo "this.responseText"; ?>;
							}
						};
						updateRequest.send();
					}
					function wipe(index){
						var cells = document.getElementById('studentList').rows[index].cells;
						cells[1].firstElementChild.value = "";
						cells[2].firstElementChild.value = "";
						cells[3].firstElementChild.value = "";
						if(index == 2){
							cells[0].firstElementChild.value = "";
							findList = [];
							showList(1, column, ascending, false);
						}
					}
					function addNewRow(mTable, index, student){
						var row = mTable.insertRow(index);
						var cNo = row.insertCell(0);
						var cName = row.insertCell(1);
						var cSurname = row.insertCell(2);
						var cDepartment = row.insertCell(3);
						
						cNo.innerHTML = student.no;
						cName.innerHTML = student.name;
						cSurname.innerHTML = student.surname;
						cDepartment.innerHTML = student.department;
						
						var cUpdate = row.insertCell(4);
						var cDelete = row.insertCell(5);
						
						var bUpdate = document.createElement("button");
						bUpdate.type = "button";
						bUpdate.className = "btn btn-primary";
						bUpdate.onclick = function(){changeRow(row)};
						bUpdate.innerHTML = "Update";
						cUpdate.append(bUpdate);
						var bDelete = document.createElement("button");
						bDelete.type = "button";
						bDelete.className = "btn btn-primary";
						bDelete.onclick = function(){del(row.rowIndex)};
						bDelete.innerHTML = "Delete";
						cDelete.append(bDelete);
					}
				</script>
			</head>
			<body>
				<table id="studentList" class="table table-striped" style="margin-top:5px;">
					<thead class='thead-dark'>
					<tr>
						<th id="hNo" style="border: 0px;border-radius: 25px;" onClick="arrange(this, this.parentElement)">Student Number</th>
						<th id="hName" style="border: 0px;border-radius: 25px;" onClick="arrange(this, this.parentElement)">Name</th>
						<th id="hSurname" style="border: 0px;border-radius: 25px;" onClick="arrange(this, this.parentElement)">Surname</th>
						<th id="hDepartment" style="border: 0px;border-radius: 25px;" onClick="arrange(this, this.parentElement)">Department</th>
					</tr>
					</thead>
					<tbody>	
						<tr style='background: rgba(150, 255, 150, .3)'>
							<td id='newNo'></td>
							<td><input id='newName' type='text' /></td>
							<td><input id='newSurname' type='text' /></td>
							<td><input id='newDepartment' type='text' /></td>
							<td><button type='button' class='btn btn-primary' onClick='wipe(1)'>Clear</button></td>
							<td><button type='button' class='btn btn-primary' onClick='newStudent()'>New Student</button></td>
						</tr>
						
						<tr style='background: rgba(150, 255, 150, .3)'>
							<td><input id='findNo' type='text'/></td>
							<td><input id='findName' type='text' /></td>
							<td><input id='findSurname' type='text' /></td>
							<td><input id='findDepartment' type='text' /></td>
							<td><button type='button' class='btn btn-primary' onClick='wipe(2)'>Clear</button></td>
							<td><button type='button' class='btn btn-primary' onClick='findStudent()'>Find</button></td>
						</tr>
					</tbody>
				</table>
				<div id="pageButtons" class='center'>
				</div>
				<script>
					var page = 1, column = "no", ascending = true;
					document.getElementById("hNo").style.color = "#8888ff";
					document.getElementById("newNo").innerHTML = nextNumber;
					window.onload = showList(page, column, ascending, false);
				</script>
			</body>
		</html><?php
		
	}

	function update($no, $ad, $soyad, $department){
		echo "updated " + $ad;
		$connect = connect();
		$query = "SELECT name, surname, department FROM students WHERE no='$no'";
		if(!$retval = mysqli_query($connect, $query)){
			echo "No student found with this number";
			return ;
		}
		$row = mysqli_fetch_array($retval);
		$eskiad = $row['name'];
		$eskisoyad = $row['surname'];
		$eskibolum = $row['department'];
		
		$query = "UPDATE students SET ";
		if($ad != $eskiad && $ad != ""){
			$query .= "name='$ad'";
			if(($soyad != $eskisoyad && $soyad != "") || ($department != $eskibolum && $department != ""))
				$query .= ",";
		}
		if($soyad != $eskisoyad && $soyad != ""){
			$query .= "surname='$soyad'";
			if($department != $eskibolum && $department != "")
				$query .= ",";
		}
		if($department != $eskibolum && $department != "")
			$query .= "department='$department'";
		$query .= " WHERE no='$no'";
		if(!$retval = mysqli_query($connect, $query))
			echo "Update operation failed" . mysqli_error($connect) . $query;
		
		mysqli_close($connect);
		
		echo "success";
	}

	function del($no){
		$connect = connect();
		$query = "DELETE FROM students WHERE no='$no'";
		if(!$retval = mysqli_query($connect, $query))
			echo "Delete operation failed";
		
		mysqli_close($connect);
	}

	function add($ad, $soyad, $department){
		echo "added " + $ad;
		$connect = connect();
		$query = "INSERT INTO students (name, surname, department) VALUES ('$ad', '$soyad', '$department')";
		if(!$retval = mysqli_query($connect, $query))
			echo "Entry couldn't be added" . mysqli_error($connect);
		
		mysqli_close($connect);
	}
	
	function getStudents(){
		$students = array();
		$connect = connect();
		$query = "SELECT * FROM students ORDER BY no ASC";
		
		$retval = mysqli_query($connect, $query);
		
		while($row = mysqli_fetch_array($retval))
			array_push($students, array("no" => $row['no'], "name" => $row['name'], "surname" => $row['surname'], "department" => $row['department']));
		
		mysqli_close($connect);
		
		return json_encode($students);
		
		//echo json_encode($students);
	}
	
	function getNextNumber(){
		$connect = connect();
		
		$query = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES 
					WHERE TABLE_SCHEMA='university' AND TABLE_NAME='students'";
		$nextNumber = mysqli_fetch_array(mysqli_query($connect, $query))[0];
		
		mysqli_close($connect);
		
		return $nextNumber;
	}
	
	function connect(){
		$host = "localhost";
		$user = "root";
		$password = null;
		$dbname = "university";
		
		$connect = mysqli_connect($host, $user, $password, $dbname);
		
		if(!$connect)
			die("Could not connect: " . mysqli_error($connect));
		
		$query = "set names 'utf8'";
		mysqli_query($connect, $query);
		
		return $connect;
	}
		
?>