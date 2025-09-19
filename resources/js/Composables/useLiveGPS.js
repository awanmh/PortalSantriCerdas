import { ref, onMounted, onUnmounted } from "vue";

export function useLiveGPS(kelasId) {
  const siswaLokasi = ref([]);
  const error = ref(null);

  let channel = null;

  onMounted(() => {
    if (!window.Echo) return;

    try {
      channel = window.Echo.private(`live-absensi.${kelasId}`);

      channel.listen('LokasiSiswaDiperbarui', (event) => {
        const index = siswaLokasi.value.findIndex(
          s => s.siswa.id === event.siswa.id
        );

        const lokasiData = {
          lat: event.lokasi.lat,
          lng: event.lokasi.lng,
          waktu: event.lokasi.waktu,
        };

        if (index >= 0) {
          siswaLokasi.value[index].lokasi = lokasiData;
        } else {
          siswaLokasi.value.push({
            siswa: event.siswa,
            lokasi: lokasiData,
          });
        }
      });
    } catch (err) {
      error.value = err.message;
    }
  });

  onUnmounted(() => {
    if (channel) channel.stopListening('LokasiSiswaDiperbarui');
  });

  return { siswaLokasi, error };
}
