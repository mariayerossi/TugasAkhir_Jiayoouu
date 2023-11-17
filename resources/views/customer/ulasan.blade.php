@extends('layouts.navbar_customer')

@section('content')
<style>
  .img-fluid {
      height: 80px;
      width: 110px;
      overflow: hidden; /* Memastikan gambar tidak melebihi kontainer */
  }
  @media (max-width: 768px) {
    .img-fluid {
      height: 60px;
      width: 90px;
      overflow: hidden; /* Memastikan gambar tidak melebihi kontainer */
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
        color: gold;
    }
</style>
<div class="container mt-5 p-5 mb-5" style="background-color: white;box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);">
    <h4>Kode Transaksi: {{$htrans->first()->kode_trans}}</h4>
    @php
        $tanggalAwal2 = $htrans->first()->tanggal_sewa;
        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
        $carbonDate = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
        $tanggal_hasil = $carbonDate->isoFormat('D MMMM YYYY');
    @endphp
    <h6>{{$tanggal_hasil}}, Pukul {{ \Carbon\Carbon::parse($htrans->first()->jam_sewa)->format('H:i') }} - {{ \Carbon\Carbon::parse($htrans->first()->jam_sewa)->addHours($htrans->first()->durasi_sewa)->format('H:i') }}</h6>
    <div class="accordion" id="accordionExample">
        @php
            $lap = DB::table('lapangan_olahraga')->where("id_lapangan","=",$htrans->first()->fk_id_lapangan)->get()->first();
            $fileLap = DB::table('files_lapangan')->where("fk_id_lapangan","=",$htrans->first()->fk_id_lapangan)->get()->first();
        @endphp
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              <img src="{{ asset('upload/' . $fileLap->nama_file_lapangan) }}" alt="" class="img-fluid"><h5 class="truncate-text ms-2">{{$lap->nama_lapangan}}</h5>
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
            <div class="accordion-body">
              <div class="row mt-3">
                <div class="col-12">
                    <h4>Beri Ulasan</h4>
                    <form action="/customer/rating/lapangan/tambahRating" method="post" id="ratingForm">
                        @csrf
                        <div class="rating-container">
                            @for($i=1; $i<=5; $i++)
                                <i class="bi bi-star star" data-rate="{{ $i }}"></i>
                            @endfor
                            <input type="hidden" name="rating" id="ratingValue">
                        </div>
                        <div class="form-group mt-3">
                            <label for="comment">Review (opsional):</label>
                            <textarea class="form-control" name="review" id="comment" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="id_lapangan" value="{{$lap->id_lapangan}}">
                        <input type="hidden" name="id_htrans" value="{{$htrans->first()->id_htrans}}">
                        <div class="d-flex justify-content-end">
                          <button type="submit" class="btn btn-success mt-2">Kirim</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              Accordion Item #2
            </button>
          </h2>
          <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
            <div class="accordion-body">
              <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingThree">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
              Accordion Item #3
            </button>
          </h2>
          <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
            <div class="accordion-body">
              <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
            </div>
          </div>
        </div>
      </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const stars = document.querySelectorAll('.star');
      const ratingValueInput = document.getElementById('ratingValue');
      const ratingForm = document.getElementById('ratingForm');

      stars.forEach(star => {
          star.addEventListener('click', function() {
              let ratingValue = this.getAttribute('data-rate');
              fillStars(ratingValue);
              ratingValueInput.value = ratingValue;
          });
      });

      ratingForm.addEventListener('submit', function(e) {
          e.preventDefault(); // Menghentikan aksi default form submit

          $.ajax({
              url: this.action,
              method: "POST",
              data: $(this).serialize(),
              success: function(response) {
                  swal("Success!", "Berhasil mengirim rating!", "success");
                  window.location.reload();
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  swal("Error!", "Gagal mengirim rating!", "error");
              }
          });
      });

      function fillStars(value) {
          stars.forEach((star, index) => {
              if (index < value) {
                  star.classList.remove('bi-star');
                  star.classList.add('bi-star-fill');
              } else {
                  star.classList.remove('bi-star-fill');
                  star.classList.add('bi-star');
              }
          });
      }
  });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection