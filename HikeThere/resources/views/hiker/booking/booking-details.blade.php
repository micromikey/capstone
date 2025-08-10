<x-app-layout>

    <div class="bg-gradient-to-br from-slate-50 via-blue-50 to-emerald-50 min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-4 md:px-8">
            <div class="bg-white rounded-2xl shadow-xl border border-emerald-100 p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Booking Trail Summary</h2>
                <form id="bookingForm" method="POST" action="#">
                    @csrf
                    <div id="inputSection">
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-emerald-800 mb-2">Hike Details:</h3>
                            <div class="border rounded-lg p-4 bg-emerald-50">
                                <div>Mountain: Mt. Pulag</div>
                                <div>Hike Date: June 6-7, 2025</div>
                                <div>Hike Price: 5,900 * 2 = 11,800</div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-emerald-800 mb-2">Customer Details:</h3>
                            <div class="border rounded-lg p-4 bg-blue-50">
                                <label class="block mb-2 text-gray-700">Fullname:</label>
                                <input type="text" name="fullname" class="w-full border rounded px-3 py-2 mb-2" required>
                                <label class="block mb-2 text-gray-700">Email:</label>
                                <input type="email" name="email" class="w-full border rounded px-3 py-2 mb-2" required>
                                <label class="block mb-2 text-gray-700">Phone:</label>
                                <input type="text" name="phone" class="w-full border rounded px-3 py-2" required>
                            </div>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-emerald-800 mb-2">Payment Details:</h3>
                            <div class="border rounded-lg p-4 bg-white">
                                <span class="text-gray-700">Please choose mode of payment.</span>
                                <div class="flex gap-4 mt-2">
                                    <button type="button" class="bg-white border rounded-lg shadow px-4 py-2 hover:bg-blue-50 transition">
                                        <img src="{{ asset('images/paypal.png') }}" alt="PayPal" class="h-8">
                                    </button>
                                    <button type="button" class="bg-white border rounded-lg shadow px-4 py-2 hover:bg-blue-50 transition">
                                        <img src="{{ asset('images/bank.png') }}" alt="Bank" class="h-8">
                                    </button>
                                    <button type="button" class="bg-white border rounded-lg shadow px-4 py-2 hover:bg-blue-50 transition">
                                        <img src="{{ asset('images/other.png') }}" alt="Other" class="h-8">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="submitBtn" class="w-full bg-emerald-700 text-white font-bold py-3 rounded-xl mt-4 hover:bg-emerald-800 transition">
                            SUBMIT
                        </button>
                    </div>
                    <div id="summarySection" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <div>
                                <h3 class="text-lg font-bold text-emerald-800 mb-2">Hike Details</h3>
                                <div class="bg-emerald-50 rounded-lg p-4 mb-4">
                                    <div class="font-semibold text-gray-700">Mountain: <span class="text-emerald-700">Mt. Pulag</span></div>
                                    <div class="text-gray-700">Hike Date: <span class="font-semibold">June 6-7, 2025</span></div>
                                    <div class="text-gray-700">Hike Price: <span class="font-semibold">5,900 * 2 = 11,800</span></div>
                                </div>
                                <h3 class="text-lg font-bold text-emerald-800 mb-2">Customer Details</h3>
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="text-gray-700">Fullname: <span id="summaryFullname" class="font-semibold"></span></div>
                                    <div class="text-gray-700">Email: <span id="summaryEmail" class="font-semibold"></span></div>
                                    <div class="text-gray-700">Phone: <span id="summaryPhone" class="font-semibold"></span></div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-emerald-800 mb-2">Payment Details</h3>
                                <div class="mb-2 text-gray-700">Please choose mode of payment:</div>
                                <div class="flex gap-4 mb-4">
                                    <button type="button" class="bg-white border rounded-lg shadow px-4 py-2">
                                        <img src="{{ asset('images/paypal.png') }}" alt="PayPal" class="h-8">
                                    </button>
                                    <button type="button" class="bg-white border rounded-lg shadow px-4 py-2">
                                        <img src="{{ asset('images/bank.png') }}" alt="Bank" class="h-8">
                                    </button>
                                    <button type="button" class="bg-white border rounded-lg shadow px-4 py-2">
                                        <img src="{{ asset('images/other.png') }}" alt="Other" class="h-8">
                                    </button>
                                </div>
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-gray-700">
                                    By submitting your payment, you acknowledge that your payment details will be processed securely in accordance with our Privacy Policy.
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-gray-700 text-sm">
                            <ol class="list-decimal ml-6 space-y-1">
                                <li>Settle your down payment: 60% of the total cost (ex. P1,000 total cost = P600 down payment)</li>
                                <li>Email us, info@hikethere.com, a copy of your transaction slip. Please include your name, destination, and the date of your hike/trip.</li>
                                <li>Payments should be accomplished within the stated deadline.</li>
                            </ol>
                            <div class="mt-3 font-semibold text-emerald-700">Thank you and See you on the trails!</div>
                        </div>
                        <button type="button" class="w-full bg-emerald-700 text-white font-bold py-3 rounded-xl mt-4 hover:bg-emerald-800 transition" disabled>
                            SUBMIT
                        </button>
                    </div>
                </form>
            </div>
        
    

    <script>
        document.getElementById('submitBtn').onclick = function() {
            // Get input values
            document.getElementById('summaryFullname').innerText = document.querySelector('input[name="fullname"]').value;
            document.getElementById('summaryEmail').innerText = document.querySelector('input[name="email"]').value;
            document.getElementById('summaryPhone').innerText = document.querySelector('input[name="phone"]').value;
            // Show summary, hide input
            document.getElementById('inputSection').classList.add('hidden');
            document.getElementById('summarySection').classList.remove('hidden');
        };
    </script>
</x-app-layout>