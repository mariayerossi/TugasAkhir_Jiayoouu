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
    <h4>Kode Transaksi: {{$htrans->first()->kode_trans}}</h4>
    @php
        $tanggalAwal2 = $htrans->first()->tanggal_sewa;
        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
        $carbonDate = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
        $tanggal_hasil = $carbonDate->isoFormat('D MMMM YYYY');
    @endphp
    <h6>{{$tanggal_hasil}}, Pukul {{ \Carbon\Carbon::parse($htrans->first()->jam_sewa)->format('H:i') }} - {{ \Carbon\Carbon::parse($htrans->first()->jam_sewa)->addHours($htrans->first()->durasi_sewa)->format('H:i') }}</h6>
    <div class="accordion mt-3" id="accordionExample">
        @php
            $lap = DB::table('lapangan_olahraga')->where("id_lapangan","=",$htrans->first()->fk_id_lapangan)->get()->first();
            $fileLap = DB::table('files_lapangan')->where("fk_id_lapangan","=",$htrans->first()->fk_id_lapangan)->get()->first();
            $ratingLap = DB::table('rating_lapangan')->where("fk_id_lapangan","=",$htrans->first()->fk_id_lapangan)->where("fk_id_htrans","=",$htrans->first()->id_htrans)->get()->first();
        @endphp
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
                          <form action="/customer/rating/alat/tambahRating" method="post" id="ratingForm">
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
                              <input type="hidden" name="id_alat" value="{{$item->fk_id_alat}}">
                              <input type="hidden" name="id_dtrans" value="{{$item->id_dtrans}}">
                              <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success mt-2">Kirim</button>
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