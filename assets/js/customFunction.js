

function editResident(that) {
    ownerid = $(that).attr('data-ownerid');
    id = $(that).attr('data-id');
    pic = $(that).attr('data-img');
    national = $(that).attr('data-nat');
    pet_name = $(that).attr('data-pname');
    OwnerName = $(that).attr('data-oname');

    bplace = $(that).attr('data-bplace');
    bdate = $(that).attr('data-bdate');
    age = $(that).attr('data-age');
    pgender = $(that).attr('data-pgender');
    bname = $(that).attr('data-bname');
    OwnerEmail = $(that).attr('data-email');
    OwnerMobileNo = $(that).attr('data-onumber');
    OwnerAddress = $(that).attr('data-oplace');
    OwnerCity = $(that).attr('data-oplace');
    OwnerZip = $(that).attr('data-zcode');
    OwnerID = $(that).attr('data-oid');
    pnotes = $(that).attr('data-pnotes');
    ptype = $(that).attr('data-ptype');
    isActive = $(that).attr('data-isActive');

    $('#identity').prop('disabled', false);
    $('#ownerid').val(ownerid);
    $('#res_id').val(id);
    
    $('#pname').val(pet_name);
    $('#oname').val(OwnerName);
    $('#bplace').val(bplace);
    $('#bdate').val(bdate);
    $('#age').val(age);
    $('#pgender').val(pgender);
    $('#email').val(OwnerEmail);
    $('#onumber').val(OwnerMobileNo);
    $('#oplace').val(OwnerAddress);
    $('#ocity').val(OwnerCity);
    $('#zcode').val(OwnerZip);
    $('#OwnerID').val(OwnerID);
    $('#pnotes').val(pnotes);
    $('#bname').val(bname);
    $('#ptype').val(ptype);
    $('#isActive').val(isActive);

    // Debugging: Log variable values
    // Debugging: Log variable values
    console.log('pic:', pic);

    // Check if pic is defined before using it
    if (pic !== undefined) {
        var str = pic;
        var n = str.includes("data:image");
        if (!n) {
            pic = 'assets/uploads/resident_profile/' + pic;
        }
        $('#image').attr('src', pic);
        console.log('Updated pic:', pic);
    } else {
        console.log('pic is undefined');
    }
}


function editOwner(that) {

    ownerid = $(that).attr('data-ownerid');
    
    OwnerName = $(that).attr('data-oname');

    

    OwnerEmail = $(that).attr('data-email');
    OwnerMobileNo = $(that).attr('data-onumber');
    OwnerAddress = $(that).attr('data-oplace');
    OwnerCity = $(that).attr('data-oplace');
    OwnerZip = $(that).attr('data-zcode');
    OwnerID = $(that).attr('data-oid');

    


    $('#ownerid').val(ownerid);

    $('#oname').val(OwnerName);
  

    $('#email').val(OwnerEmail);
    $('#onumber').val(OwnerMobileNo);
    $('#oplace').val(OwnerAddress);
    $('#ocity').val(OwnerCity);
    $('#zcode').val(OwnerZip);
    $('#OwnerID').val(OwnerID);
   
    

    
}





function editOperation2(that) {
    id = $(that).attr('data-id');
    petName = $(that).attr('data-petName');
    petOwner = $(that).attr('data-petOwner');
    operationType = $(that).attr('data-operationType');
    date = $(that).attr('data-date');
    time = $(that).attr('data-time');
    details = $(that).attr('data-details');
    status = $(that).attr('data-status');

    $('#set_id').val(id);
    $('#petName').val(petName);  // Assuming petName is the correct variable
    $('#petOwner').val(petOwner);  // Assuming petOwner is the correct variable
    $('#operationType').val(operationType);  // Assuming operationType is the correct variable
    $('#date').val(date);
    $('#time').val(time);
    $('#details').val(details);
    $('#status').val(status);
}


$('.vstatus').change(function(){
    var val = $(this).val();
    if(val=='No'){
        $('.indetity').prop('disabled', 'disabled');
    }else{
        $('.indetity').prop('disabled', false);
    }
});

$(".toggle-password").click(function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
      input.attr("type", "text");
    } else {
      input.attr("type", "password");
    }
});


Webcam.set({
    height: 250,
    image_format: 'jpeg',
    jpeg_quality: 90
});

$('#open_cam').click(function(){
    Webcam.attach( '#my_camera' );
});

function save_photo() {
    // actually snap photo (from preview freeze) and display it
    Webcam.snap( function(data_uri) {
        // display results in page
        document.getElementById('my_camera').innerHTML = 
        '<img src="'+data_uri+'"/>';
        document.getElementById('profileImage').innerHTML = 
        '<input type="hidden" name="profileimg" id="profileImage" value="'+data_uri+'"/>';
    } );
}

$('#open_cam1').click(function(){
    Webcam.attach( '#my_camera1' );
});

function save_photo1() {
    // actually snap photo (from preview freeze) and display it
    Webcam.snap( function(data_uri) {
        // display results in page
        document.getElementById('my_camera1').innerHTML = 
        '<img src="'+data_uri+'"/>';
        document.getElementById('profileImage1').innerHTML = 
        '<input type="hidden" name="profileimg" id="profileImage1" value="'+data_uri+'"/>';
    } );
}

function goBack() {
  window.history.go(-1);
}