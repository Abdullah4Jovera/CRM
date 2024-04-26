@php
    use App\Models\Utility;
    $get_cookie = \App\Models\Utility::getCookieSetting();

@endphp
<!-- [ Main Content ] end -->
<footer class="dash-footer">
    <div class="footer-wrapper">
        <div class="py-1">
            <span class="text-muted">  {{(Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :  __('Copyright ERPGO') }} {{ date('Y') }}</span>
        </div>
    </div>
</footer>
<div class="modal fade" id="Calcul" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content loan-calculator">
                <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Calculator</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
            <div class="modal-body ">
                <div class="top">
                    {{-- <h2>Mortgage Calculator</h2> --}}
                    <form action="#">
                        <div class="group">
                        <div class="title">Amount</div>
                            <input type="text" oninput="cal()" id="loanAmount" value="1000000" class="loan-amount form-control">
                        </div>
                        <div class="group">
                        <div class="title">Interest Rate</div>
                            <input type="text" oninput="cal()" id="annualInterestRate" value="4.80" class="interest-rate form-control">
                        </div>
                        <div class="group">
                            <div class="title">Tenure Years</div>
                            <input type="text" oninput="cal()" min="0" id="loanTerm" value="25" class="loan-tenure form-control">
                        </div>
                        <div class="group">
                            <div class="title">Tenure Months</div>
                            <input type="text" oninput="cal()" id="loanTermMonths" min="0" value="0" class="loan-tenure form-control">
                        </div>
                    </form>


                </div>
                <div class="result">
                    <div class="left">
                        <div class="loan-emi">
                            <h3 >Monthly Payment</h3>
                            <div id="monthlyEMI" class="value">5,730</div>
                        </div>
                        <div class="total-interest">
                            <h3 >Total Interest Payable</h3>
                            <div id="totalInterest" class="value">718,991</div>
                        </div>
                        <div class="total-amount">
                            <h3 >Total Amount</h3>
                            <div id="totalAmount" class="value">1,718,991</div>
                        </div>


                    </div>


                </div>
                <div class="top">

                    <form action="#">
                        <div class="group ">
                            <div class="title" for="emiAmount">Desired EMI:</div>
                            <input class="form-control" oninput="calculateLoanAmount()" type="text" id="emiAmount" value="5730">
                        </div>
                        <div class="group ">
                            <div class="title" for="emiannualInterestRate">Interest Rate (%):</div>
                            <input class="form-control" oninput="calculateLoanAmount()" type="number" id="emiannualInterestRate" step="0.01" placeholder="Enter Annual Interest Rate"  value="4.80">
                        </div>
                        <div class="group ">
                            <div class="title" for="loanTermYears">Tenure Years</div>
                            <input class="form-control" oninput="calculateLoanAmount()"  step="0.01" type="text" id="loanTermYears" value="25">
                        </div>
                         <div class="group">
                            <div class="title">Tenure Months</div>
                            <input type="text" id="emiloanTermMonths" min="0" value="0" class="loan-tenure form-control" oninput="calculateLoanAmount()">
                        </div>
                    </form>
                </div>
                <div class="result" style="margin-top:2em; border-bottom: 1px solid rgba(20, 33, 61, 0.2) !important;">
                    <div class="total-amount" style="padding-left: 2em; ">
                        <h3 style="font-size: 16px;font-weight: 400;margin-bottom:8px; "> Desired Loan Amount Approx. (By EMI):</h3>
                        <div style="font-size: 30px; font-weight: 900; padding-bottom: 10px" id="calculatedLoanAmount">1,000,005.30</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="EMICAl" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content loan-calculator">
                <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Calculator</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
            <div class="modal-body ">
                <div class="">
                    <div class="top">
                        {{-- <h2>Mortgage Calculator</h2> --}}
                        {{-- <h1>Mortgage Calculator</h1> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var modalId = document.getElementById('modalId');

    modalId.addEventListener('show.bs.modal', function (event) {
          // Button that triggered the modal
          let button = event.relatedTarget;
          // Extract info from data-bs-* attributes
          let recipient = button.getAttribute('data-bs-whatever');

        // Use above variables to manipulate the DOM
    });
</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js@3.6.2/dist/chart.min.js"></script> --}}



<!-- Warning Section Ends -->
<!-- Required Js -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.form.js') }}"></script>
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/dash.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>

<script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>

<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>

<!-- Apex Chart -->
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/flatpickr.min.js') }}"></script>

<script src="{{ asset('js/jscolor.js') }}"></script>

<script src="{{ asset('js/popper.min.js') }}"></script>
{{--<script src="{{ asset ('js/bootstrap.min.js') }}"></script>--}}

<script>
    var site_currency_symbol_position = '{{ \App\Models\Utility::getValByName('site_currency_symbol_position') }}';
    var site_currency_symbol = '{{ \App\Models\Utility::getValByName('site_currency_symbol') }}';
</script>
<script src="{{ asset('js/custom.js') }}"></script>

@if($message = Session::get('success'))
    <script>
        show_toastr('success', '{!! $message !!}');
    </script>
@endif
@if($message = Session::get('error'))
    <script>
        show_toastr('error', '{!! $message !!}');
    </script>
@endif
@if($get_cookie['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif
@stack('script-page')

@stack('old-datatable-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function cal() {
      var loanAmount = parseFloat($("#loanAmount").val());
      var annualInterestRate = parseFloat($("#annualInterestRate").val()) / 100;
      var loanTerm = parseFloat($("#loanTerm").val());
      var loanTermMonths = $("#loanTermMonths").val();
      var monthlyInterestRate = annualInterestRate / 12;
      var numberOfPayments = (parseInt(loanTerm || 0) * 12) + parseInt(loanTermMonths || 0);
      var monthlyEMI = loanAmount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments) / (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
      var totalAmount = monthlyEMI * numberOfPayments;
      var totalInterest = totalAmount - loanAmount;
      $("#totalAmount").html(Number(parseFloat(totalAmount).toFixed(2)).toLocaleString('en'));
      $("#totalInterest").html(Number(parseFloat(totalInterest).toFixed(2)).toLocaleString('en'));
      $("#monthlyEMI").html(Number(parseFloat(monthlyEMI).toFixed(2)).toLocaleString('en'));
    };

</script>
<script>

    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function () {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }

    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];

        c.addEventListener("click", function (event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }


    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
</script>

<script>

        function calculateLoanAmount() {
            var emiAmount = parseFloat($("#emiAmount").val());
            var annualInterestRate = parseFloat($("#emiannualInterestRate").val());
            var loanTermYears = $("#loanTermYears").val();
            var emiloanTermMonths = $("#emiloanTermMonths").val();
                // Convert annual interest rate to monthly and calculate total number of payments
                var monthlyInterestRate = annualInterestRate / (12 * 100);
                var totalPayments = (parseInt(loanTermYears || 0) * 12) + parseInt(emiloanTermMonths || 0);

                // Calculate loan amount using the formula
                var loanAmount = (emiAmount * (((1 + monthlyInterestRate) ** totalPayments) - 1) / (monthlyInterestRate * ((1 + monthlyInterestRate) ** totalPayments))).toFixed(2);

                // Display result
                $("#calculatedLoanAmount").text(Number(parseFloat(loanAmount).toFixed(2)).toLocaleString('en'));

        }
        $(function () {
            let authId = `{{\Auth::user()->id}}`;
            var url = "{{ route('get_notif.dashboard', ":id") }}";
            url = url.replace(':id', authId);
            $.ajax({
                type: "GET",
                url: url,
                // data: ,
                // dataType: "dataType",
                contentType: false,
                processData: false,
                success: function (response) {
                    $('.notifications').html((response.get_data).length);
                    $('.notifications').addClass(`bg-danger dash-h-badge message-toggle-msg  message-counter custom_messanger_counter beep`);
                    $('.notifications_dropdown').html(' ');
                    for (let ind = 0; ind < (response.data_html).length; ind++) {
                        const ele = response.data_html[ind];
                        $('.notifications_dropdown').append(ele)
                    }

                }
            });
        });
        setInterval( function(){
            let authId = `{{\Auth::user()->id}}`;
            var url = "{{ route('get_notif.dashboard', ":id") }}";
            url = url.replace(':id', authId);
            $.ajax({
                type: "GET",
                url: url,
                // data: ,
                // dataType: "dataType",
                contentType: false,
                processData: false,
                success: function (response) {
                    $('.notifications').html((response.get_data).length);
                    $('.notifications').addClass(`bg-danger dash-h-badge message-toggle-msg  message-counter custom_messanger_counter beep`);
                    $('.notifications_dropdown').html(' ');
                    for (let ind = 0; ind < (response.data_html).length; ind++) {
                        const ele = response.data_html[ind];
                        $('.notifications_dropdown').append(ele)
                    }
                }
            });
        }  , 60000 );

        // $(function () {

        // });

</script>
