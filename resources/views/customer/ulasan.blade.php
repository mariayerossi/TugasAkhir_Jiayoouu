@extends('layouts.navbar_customer')

@section('content')
<style>
  .img-fluid {
      width: calc(4/3 * 60px); /* 4:3 aspect ratio for a height of 60px */
      max-width: 100%; /* Set a maximum width for responsiveness */
      overflow: hidden; /* Memastikan gambar tidak melebihi kontainer */
  }
  .img-fluid2 {
      width: 60px; /* 1:1 aspect ratio for a height of 60px */
      max-width: 100%; /* Set a maximum width for responsiveness */
      overflow: hidden; /* Memastikan gambar tidak melebihi kontainer */
  }
  @media (max-width: 768px) {
    .img-fluid {
      height: 60px;
      width: 90px;
      overflow: hidden; /* Memastikan gam bar tidak melebihi kontainer */
    }
  }
  .truncate-text {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
      display: block;
  }
  .star:not(.filled) {
        font-size: 24px;
        cursor: pointer;
    }
    .bi-star-fill {
      font-size: 24px;
        color: gold;
    }
</style>
<div class="container mt-5 p-5 mb-5" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-start d-none d-md-block mb-4">
        <a href="javascript:history.back()"><i class="bi bi-chevron-left me-2"></i>Kembali</a>
    </div>
    <h4>Kode Transaksi: {{$htrans->first()->kode_trans}}</h4>
    @php
        $tanggalAwal2 = $htrans->first()->tanggal_sewa;
        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
        $carbonDate = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
        $tanggal_hasil = $carbonDate->isoFormat('D MMMM YYYY');
    @endphp
    <h6>{{$tanggal_hasil}}, Pukul {{ \Carbon\Carbon::parse($htrans->first()->jam_sewa)->format('H:i') }} - {{ \Carbon\Carbon::parse($htrans->first()->jam_sewa)->addHours($htrans->first()->durasi_sewa)->format('H:i') }}</h6>
    <div class="accordion mt-3" id="accordionExample">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              <img src="{{ asset('upload/' . $fileLap->nama_file_lapangan) }}" alt="" class="img-fluid"><h5 class="truncate-text ms-2">{{$lap->nama_lapangan}}</h5>
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
            <div class="accordion-body">
              @if ($ratingLap == null)
                <div class="row mt-3">
                  <div class="col-12">
                      <h4>Beri Ulasan</h4>
                      <form action="/customer/rating/lapangan/tambahRating" method="post" id="ratingForm_field">
                          @csrf
                          <div class="rating-container">
                              @for($i=1; $i<=5; $i++)
                                  <i class="bi bi-star star" data-rate="{{ $i }}" data-field-type="field" data-id="lap-{{$lap->id_lapangan}}"></i>
                              @endfor
                              <input type="hidden" name="rating" id="lap-{{$lap->id_lapangan}}" data-id="lap-{{$lap->id_lapangan}}">
                          </div>
                          <div class="form-group mt-3">
                            <label for="comment">Review (opsional):</label>
                            <textarea class="form-control" name="review" id="reviewValue" data-id="lap-{{$lap->id_lapangan}}" rows="3"></textarea>
                          </div>
                          <div class="form-group mt-3">
                            <label for="hide" class="me-3">Sembunyikan Nama</label>
                            <svg id="toggleSwitch" data-id="lap-{{$lap->id_lapangan}}" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-toggle-off" viewBox="0 0 16 16" style="color: #007466" onclick="hideFunction(this)">
                              <path d="M11 4a4 4 0 0 1 0 8H8a5 5 0 0 0 2-4 5 5 0 0 0-2-4zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8M0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5"/>
                            </svg>
                            <span id="toggleLabel" data-id="lap-{{$lap->id_lapangan}}" class="ml-2 ms-2">Non Aktif</span>
                            <input type="hidden" data-id="lap-{{$lap->id_lapangan}}" id="statusInput" name="status" value="Tidak">
                          </div>
                          <input type="hidden" name="id_lapangan" value="{{$lap->id_lapangan}}">
                          <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                          <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success mt-2">Kirim</button>
                          </div>
                      </form>
                  </div>
                </div>
              @else
                <h4 class="mt-4">Ulasan Anda:</h4>
                @for ($i = 1; $i <= 5; $i++)
                  @if ($i <= $ratingLap->rating)
                      <i class="bi bi-star-fill"></i>
                  @else
                      <i class="bi bi-star"></i>
                  @endif
                @endfor
                @if ($ratingLap->review != null)
                  <h6 class="mt-2 mb-4">{{$ratingLap->review}}</h6>
                @endif
              @endif
            </div>
          </div>
        </div>

        @if (!$dtrans->isEmpty())
          @foreach ($dtrans as $item)
            @php
              $alat = DB::table('alat_olahraga')->where("id_alat", "=", $item->fk_id_alat)->get()->first();
              $filesAlat = DB::table('files_alat')->where("fk_id_alat", "=", $item->fk_id_alat)->get()->first();
              $ratingAlat = DB::table('rating_alat')->where("fk_id_alat", "=", $item->fk_id_alat)->where("fk_id_dtrans","=",$item->id_dtrans)->get()->first();
              $collapseId = 'collapse_' . $alat->id_alat;
            @endphp
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading{{ $alat->id_alat }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                  <img src="{{ asset('upload/' . $filesAlat->nama_file_alat) }}" alt="" class="img-fluid2"><h5 class="truncate-text ms-2">{{ $alat->nama_alat }}</h5>
                </button>
              </h2>
              <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $alat->id_alat }}" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  @if ($ratingAlat == null)
                    <div class="row mt-3">
                      <div class="col-12">
                          <h4>Beri Ulasan</h4>
                          <form action="/customer/rating/alat/tambahRating" method="post" id="ratingForm_equipment_{{$item->fk_id_alat}}" class="ratingForm_equipment">
                              @csrf
                              <div class="rating-container">
                                  @for($i=1; $i<=5; $i++)
                                      <i class="bi bi-star star" data-rate="{{ $i }}" data-field-type="equipment" data-id="al-{{$item->fk_id_alat}}"></i>
                                  @endfor
                                  <input type="hidden" name="rating" id="al-{{$item->fk_id_alat}}" data-id="al-{{$item->fk_id_alat}}">
                              </div>
                              <div class="form-group mt-3">
                                <label for="comment">Review (opsional):</label>
                                <textarea class="form-control" name="review" id="reviewValue" data-id="al-{{$item->fk_id_alat}}" rows="3"></textarea>
                              </div>
                              <div class="form-group mt-3">
                                <label for="hide" class="me-3">Sembunyikan Nama</label>
                                <svg id="toggleSwitch" data-id="al-{{$item->fk_id_alat}}" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-toggle-off" viewBox="0 0 16 16" style="color: #007466" onclick="hideFunction(this)">
                                  <path d="M11 4a4 4 0 0 1 0 8H8a5 5 0 0 0 2-4 5 5 0 0 0-2-4zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8M0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5"/>
                                </svg>
                                <span id="toggleLabel" data-id="al-{{$item->fk_id_alat}}" class="ml-2 ms-2">Non Aktif</span>
                                <input type="hidden" data-id="al-{{$item->fk_id_alat}}" id="statusInput" name="status" value="Tidak">
                              </div>
                              <input type="hidden" name="id_alat" value="{{$item->fk_id_alat}}">
                              <input type="hidden" name="id_dtrans" value="{{$item->id_dtrans}}">
                              <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-success mt-2 btn_submit" data-id="ratingForm_equipment_{{$item->fk_id_alat}}">Kirim</button>
                              </div>
                          </form>
                      </div>
                    </div>
                  @else
                    <h4 class="mt-4">Ulasan Anda:</h4>
                    @for ($i = 1; $i <= 5; $i++)
                      @if ($i <= $ratingAlat->rating)
                          <i class="bi bi-star-fill"></i>
                      @else
                          <i class="bi bi-star"></i>
                      @endif
                    @endfor
                    @if ($ratingAlat->review != null)
                      <h6 class="mt-2 mb-4">{{$ratingAlat->review}}</h6>
                    @endif
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>
</div>
<script>
  function hideFunction(button){
    let id = button.getAttribute('data-id');
    console.log(id);

    var label = document.querySelector(`#toggleLabel[data-id="${id}"]`);
    var svgElement = document.querySelector(`#toggleSwitch[data-id="${id}"]`);
    var statusInput = document.querySelector(`#statusInput[data-id="${id}"]`);
    if (label.textContent === "Aktif") {
        label.textContent = "Non Aktif";
        svgElement.setAttribute("class", "bi bi-toggle-off");
        svgElement.innerHTML = "<path d='M11 4a4 4 0 0 1 0 8H8a4.992 4.992 0 0 0 2-4 4.992 4.992 0 0 0-2-4h3zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zM0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5z'/>";
        statusInput.value = "Tidak";
    } else {
        label.textContent = "Aktif";
        svgElement.setAttribute("class", "bi bi-toggle-on");
        svgElement.innerHTML = "<path d='M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z'/>";
        statusInput.value = "Ya";
    }
  }

  $(document).on("click",".btn_submit",function(){
    // alert("halo");
  var form = $(this).attr("data-id");
          $.ajax({
              url: "/customer/rating/alat/tambahRating",
              method: "POST",
              data: $('#'+form).serialize(),
              success: function(response) {
                // alert("halo");
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    window.location.reload();
                }
                else {
                    swal({
                        title: "Error!",
                        text: response.message,
                        type: "error",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  swal("Error!", "Gagal mengirim rating!", "error");
              }
          });
});

  document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingForms = document.querySelectorAll('.rating-form');

    stars.forEach(star => {
    star.addEventListener('click', function() {
        let ratingValue = this.getAttribute('data-rate');
        let fieldType = this.getAttribute('data-field-type'); // Get the field type
        let id = this.getAttribute('data-id'); // Get the id
        let ratingForm = document.querySelector(`#ratingForm_${fieldType}[data-id="${id}"]`); // Select the appropriate form with matching id
        fillStars(ratingValue, fieldType, id); // Pass id to the fillStars function
        // ratingForm.querySelector(`#ratingValue[data-id="${id}"]`).value = ratingValue;
        console.log(id);
        $('#'+id).val(ratingValue);
    });
});

      document.querySelector(`#ratingForm_field`).addEventListener('submit', function(e) {
          e.preventDefault(); // Menghentikan aksi default form submit
          // alert("halo");
          $.ajax({
              url: "/customer/rating/lapangan/tambahRating",
              method: "POST",
              data: $(this).serialize(),
              success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    window.location.reload();
                }
                else {
                    swal({
                        title: "Error!",
                        text: response.message,
                        type: "error",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  swal("Error!", "Gagal mengirim rating!", "error");
              }
          });
      });

      function fillStars(value, fieldType, id) {
        stars.forEach(star => {
            let starFieldType = star.getAttribute('data-field-type');
            let starDataId = star.getAttribute('data-id');
            if (starFieldType === fieldType && starDataId === id) { // Check if the field type matches
                if (star.getAttribute('data-rate') <= value) {
                    star.classList.add('bi-star-fill');
                    star.classList.remove('bi-star');
                } else {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                }
            }
        });
    }
  });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection