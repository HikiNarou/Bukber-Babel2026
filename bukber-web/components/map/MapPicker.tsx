"use client";

import "leaflet/dist/leaflet.css";
import { MapContainer, TileLayer, CircleMarker, useMapEvents } from "react-leaflet";

interface MapPickerProps {
  latitude?: number;
  longitude?: number;
  onChange: (coords: { latitude: number; longitude: number }) => void;
}

function Picker({
  latitude,
  longitude,
  onChange,
}: {
  latitude?: number;
  longitude?: number;
  onChange: (coords: { latitude: number; longitude: number }) => void;
}) {
  useMapEvents({
    click: (event) => {
      onChange({
        latitude: event.latlng.lat,
        longitude: event.latlng.lng,
      });
    },
  });

  const hasValidCoords = Number.isFinite(latitude) && Number.isFinite(longitude);
  if (!hasValidCoords) {
    return null;
  }
  const markerCenter: [number, number] = [latitude as number, longitude as number];

  return (
    <CircleMarker center={markerCenter} radius={10} pathOptions={{ color: "#2f6df2", fillColor: "#84adff" }} />
  );
}

export function MapPicker({ latitude, longitude, onChange }: MapPickerProps) {
  const safeLatitude = Number.isFinite(latitude) ? latitude : undefined;
  const safeLongitude = Number.isFinite(longitude) ? longitude : undefined;
  const center: [number, number] = [
    safeLatitude ?? -6.2088,
    safeLongitude ?? 106.8456,
  ];

  return (
    <div className="overflow-hidden rounded-2xl border border-[#2e4667] md:rounded-3xl">
      <MapContainer center={center} zoom={13} scrollWheelZoom className="h-60 w-full md:h-72">
        <TileLayer
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />
        <Picker latitude={safeLatitude} longitude={safeLongitude} onChange={onChange} />
      </MapContainer>
    </div>
  );
}
