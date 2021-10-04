<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="account-wholebody">
  		<h2 class="account-heading-txt my-account">MY ACCOUNT</h2>
	    <nav class="accound-det-nav">
		  <div class="nav nav-tabs accound-det-navtab" id="nav-tab" role="tablist">
		    <a class="nav-item nav-link active padl-0" data-toggle="tab" href="#nav-myaccount">MY ACCOUNT</a>
		    <a class="nav-item nav-link" data-toggle="tab" href="#nav-mydetails" role="tab">MY DETAILS</a>
		    <a class="nav-item nav-link text-right logout-txt"  href="#nav-contact">Logout</a>
		  </div>
		</nav>
		<div class="tab-content account-tabcont">
		  <div class="tab-pane fade show active" id="nav-myaccount" role="tabpanel">
		  	<div class="accont-contdiv">
		  		<div class="accont-left-contdiv">
		  			<table class="table accont-left-table borderless">					  
					  <tbody>
					    <tr>					      
					      <td><p class="dettitle">Username</p></td>
					      <td>:</td>
					      <td><p class="dettitle">bensherman</p></td>
					    </tr>
					    <tr>					      
					      <td><p class="dettitle">Password </p></td>
					      <td>:</td>
					      <td><p class="dettitle">********** <span class="viespan"><a href="">(view password)</a></span></p></td>
					    </tr>
					    <tr>					      
					      <td><p class="dettitle">Email </p></td>
					      <td>:</td>
					      <td><p class="dettitle">bensherman@schlumberger.com</p></td>
					    </tr>
					    <tr>					      
					      <td><p class="dettitle">Phone number</p> </td>
					      <td>:</td>
					      <td><p class="clckset" id="phone-num-set">(Click to set)</p>
					      	<input type="text" name="" id="phone-num-setinput">
					      </td>
					    </tr>
					    <tr>					      
					      <td><p class="dettitle">Company phone number</p> </td>
					      <td>:</td>
					      <td><p class="clckset" id="comphone-num-set">(Click to set)</p>
					      	<input type="text" name="" id="comphone-num-setinput">
					      </td>
					    </tr>
					  </tbody>
					</table>
		  		</div>
		  		<div class="accont-right-contdiv">
		  			<div class="profle-imgbox">
		  				<img src="change-pic.svg" class="profle-img">
		  				<p class="text-center">
		  					<label for="files" class="btn chang-pictxt">Change Picture</label>
      						<input id="files" style="visibility:hidden;" type="file">
      					</p>
		  			</div>
		  		</div>
		  	</div>
		  	<div class="orde-detsec">
		  		<table class="table orde-det-table text-center">
				  <thead>
				    <tr class="orde-det-table-head">
				      <th scope="col">ORDER ID</th>
				      <th scope="col">EVENT</th>
				      <th scope="col">TICKET</th>
				      <th scope="col">STATUS</th>
				      <th scope="col">RECEIPT/INVOICE</th>
				      <th scope="col">EVENT DETAILS</th>
				    </tr>
				  </thead>
				  <tbody>
				    <tr>				      
				      <td>#0000013</td>
				      <td>Negotiate To Win</td>
				      <td>3+1</td>
				      <td>Processing</td>
				      <td>-</td>
				      <td>-</td>
				    </tr>
				    <tr>				      
				      <td>#0000472</td>
				      <td>Leadership Series</td>
				      <td>1</td>
				      <td>Paid</td>
				      <td><a href="">Download</a></td>
				      <td>Passed</td>
				    </tr>
				    <tr>				      
				      <td>#0000472</td>
				      <td>Leadership Series</td>
				      <td>1</td>
				      <td>Paid</td>
				      <td><a href="">Download</a></td>
				      <td>Passed</td>
				    </tr>				    
				  </tbody>
				</table>
		  	</div>
		  </div>

		  <div class="tab-pane fade" id="nav-mydetails" role="tabpanel">
		  	<div class="nav-mydetails-cont">
		  		<div class="profle-imgbox">
		  				<img src="change-pic.svg" class="profle-img">
		  				<p class="text-center">
		  					<label for="files" class="btn chang-pictxt">Change Picture</label>
      						<input id="files" style="visibility:hidden;" type="file">
      					</p>
		  		</div>
		  		<div class="accdetails-sec">
		  			<table class="table accdetails-sec-table">					  
					  <tbody>
					    <tr>					      
					      <td><p class="dettitle">Name</p></td>
					      <td><p class="dettitle">Ben Sherman</p></td>
					      <td><a href="" class="acc-edit"><span class="acc-edit-icon"><i class="fas fa-pen"></i></span>Edit</a></td>
					    </tr>
					    <tr>					      
					      <td><p class="dettitle">Job Title</p></td>
					      <td><p class="dettitle">Managing Director</p></td>
					      <td><a href="" class="acc-edit"><span class="acc-edit-icon"><i class="fas fa-pen"></i></span>Edit</a></td>
					    </tr>
					    <tr>					      
					      <td><p class="dettitle">Company</p></td>
					      <td><p class="dettitle">Schlumberger</p></td>
					      <td><a href="" class="acc-edit"><span class="acc-edit-icon"><i class="fas fa-pen"></i></span>Edit</a></td>
					    </tr>
					    <tr>					      
					      <td><p class="dettitle">Email</p></td>
					      <td><p class="dettitle">bensherman@schlumberger.com</p></td>
					      <td><a href="" class="acc-edit"><span class="acc-edit-icon"><i class="fas fa-pen"></i></span>Edit</a></td>
					    </tr>
					    <tr>					      
					      <td><p class="dettitle">Contact</p></td>
					      <td><p class="dettitle">+60123456789</p></td>
					      <td><a href="" class="acc-edit"><span class="acc-edit-icon"><i class="fas fa-pen"></i></span>Edit</a></td>
					    </tr>					    
					  </tbody>
					</table>
		  		</div>
		  	</div>
		  </div>		  
		</div>
	</div>