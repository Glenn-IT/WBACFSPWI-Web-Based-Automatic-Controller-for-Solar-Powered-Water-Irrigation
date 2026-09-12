# Diagnostic Report: HW-080 Sensor Reading Drop Below 568 & JSN-SR04T Upgrade Guide

## 1. Problem Diagnosis & Mathematical Explanation

### The Observed Phenomenon
> **Symptom:** During automated testing, the HW-080 Surface Ponding Sensor ADC reading drops past the calibrated full-mark (`568`) down to `~500` (or lower), causing the system to calculate and report **100.0%** water level even though the physical water depth is only at the **50.0%** mark (~7–8 cm).

### Mathematical Explanation in the Firmware
In the calibrated 3-point piecewise linear interpolation function:
```cpp
// Stage 1: Dry air (1020) to Middle depth (663) -> Maps 0.0% to 50.0%
// Stage 2: Middle depth (663) to Full depth (568) -> Maps 50.0% to 100.0%
float pct = 50.0 + 50.0 * (float)(HW080_RAW_MID - raw) / (float)(HW080_RAW_MID - HW080_RAW_WET);
return constrain(pct, 0.0, 100.0);
```

* When the physical water is at 50%, the ADC reading was calibrated to be **`663`**.
* However, because the raw ADC unexpectedly plunged down to **`500`**:
  $$\text{pct} = 50.0 + 50.0 \times \frac{663 - 500}{663 - 568} = 50.0 + 50.0 \times \frac{163}{95} \approx 50.0 + 85.8 = 135.8\%$$
* The `constrain(pct, 0.0, 100.0)` function caps this value to **`100.0%`**.
* As a result, the controller assumes the field or test container is completely flooded and shuts off the pump prematurely.

---

## 2. Root Cause Analysis: Why Did the Raw ADC Drop to 500?

The HW-080 is a **resistive contact sensor**. It operates by exposing parallel etched copper tracks directly to the liquid. Four physical phenomena contribute to the reading collapsing from 663 down to 500:

### Cause 1: Electrolysis & Electrochemical Mineral Buildup (Primary Culprit)
* Passing DC electricity continuously through bare copper immersed in conductive water causes rapid **electrolytic decomposition**.
* Copper ions dissolve into the surrounding water boundary layer, while conductive salts and minerals from the water plate onto the electrode surface.
* Within 15–45 minutes of continuous immersion under active voltage, the electrical resistance of the trace junction drops dramatically, causing the ADC to read far lower values (higher conductance) for the exact same physical water height.

### Cause 2: Water Splashing & Capillary Surface Film ("Creeping Moisture")
* When the DC pump fills the container, turbulence and water discharge splash droplets onto the upper dry half of the probe board.
* Furthermore, fiberglass PCB material exhibits **capillary action**: a microscopic water film wicks upward along the sensor traces above the true resting water level.
* Because the sensor cannot distinguish between 1 mm of splashing film and 8 cm of bulk submerged water, the wet film completes the circuit across all parallel traces simultaneously, dragging the raw ADC down below 568.

### Cause 3: Water Conductivity Variations (TDS Drift)
* Pure distilled water is an electrical insulator; mineralized water, tap water, and especially runoff water containing soil and fertilizer ions are strong electrical conductors.
* If initial calibration was conducted in cleaner water and the testing water contains dissolved solids from soil, the electrical resistance drops significantly, yielding much lower ADC numbers.

### Cause 4: Header Pin Moisture Ingress
* The 3-pin header (VCC, GND, AO) sits at the top of the probe. If water droplets or high condensation bridge these pins, current bypasses the measuring traces entirely, sinking the analog signal toward ground or reference potential.

---

## 3. Sensor Comparison: HW-080 vs. JSN-SR04T

| Evaluation Metric | HW-080 (Resistive Contact Probe) | JSN-SR04T (Waterproof Ultrasonic Transducer) |
| :--- | :--- | :--- |
| **Measurement Principle** | DC electrical resistance across copper traces | Acoustic Time-of-Flight (speed of sound in air) |
| **Contact with Water** | Direct immersion (Must be submerged) | **Non-contact** (Hangs above water surface) |
| **Corrosion & Electrolysis** | ❌ Severe; traces degrade quickly | **None; transducer never touches water** |
| **Sensitivity to Mud / TDS** | ❌ Drifts drastically with water purity | **Completely unaffected by water quality** |
| **Splash / Ripple Vulnerability** | ❌ False 100% reading from surface droplets | **Unaffected by droplets on container walls** |
| **Weather / Water Resistance** | ❌ Exposed PCB and bare header pins | **IP67 waterproof sealed transducer** |
| **Linearity** | Piecewise non-linear curve | **True linear distance (Centimeters)** |
| **Mounting Constraint** | Fastened inside water reservoir | **Requires 20–25 cm clearance (Blind Zone)** |
| **Longevity in Irrigation** | Days to weeks | **Years of continuous operation** |

---

## 4. JSN-SR04T Architectural Specifications & Operating Principles

### 1. Acoustic Time-of-Flight Calculation
The sensor emits an ultrasonic pulse at $40\text{ kHz}$ and measures the round-trip echo time ($\Delta t$ in microseconds):

$$\text{Distance to Water Surface (cm)} = \frac{\Delta t \times 0.0343\text{ cm/µs}}{2}$$

As water enters the container, the distance between the overhead transducer and the water surface **decreases**.

### 2. The Critical "Blind Zone" (Dead Band)
* **Minimum Operational Distance:** The JSN-SR04T cannot detect objects closer than **`20 cm to 25 cm`**.
* If water rises within 20 cm of the transducer face, the echo returns before the receiver has finished damping its own transmission ringing, resulting in erratic or maximum readings (~0 cm or >400 cm).
* **Mounting Requirement:** The probe **must be mounted on a bracket at least 25 cm to 30 cm above the container's 100% maximum flood line**.

```
    [ JSN-SR04T Transducer ]
               |
               |  <--- 25 cm - 30 cm Dead Zone Clearance (Always Air)
               |
    =======================  <--- Maximum Water Level (100% Flood Depth)
    |                     |
    |  Water Target (50%) |  <--- Distance = Clearance + Remaining Depth
    |                     |
    |  Refill Level (45%) |
    |                     |
    =======================  <--- Container Floor (0% Empty)
```

### 3. Acoustic Beam Cone Angle
* The JSN-SR04T produces a conical acoustic beam roughly **$45^\circ$ to $75^\circ$** wide.
* Ensure the path directly below the sensor is clear of the container's side walls, hoses, or pump wiring to prevent false reflections.

---

## 5. Proposed Hardware Pinout & Wiring Plan

The JSN-SR04T interface module requires 5V power and 2 digital GPIO pins:

| JSN-SR04T Pin | Connected To | Voltage Level | Description |
| :--- | :--- | :---: | :--- |
| **`5V` (VCC)** | LM2596 Star 5.0V Rail (`+`) | 5.0V DC | Module operating power (~30 mA) |
| **`GND`** | Star Ground Rail (`-`) | 0.0V (Common) | Common system ground return |
| **`TRIG`** | Arduino Uno **`Pin A1`** (configured as `OUTPUT`) | 5.0V TTL | 10 µs high pulse to initiate ping |
| **`ECHO`** | Arduino Uno **`Pin A4`** (configured as `INPUT`) | 5.0V TTL | High pulse duration proportional to distance |

> [!NOTE]
> On the Arduino Uno, analog pins `A0` through `A5` can function identically to digital pins `D14` through `D19`. Pin `A1` (previously used for the HW-080 analog input) can be repurposed directly as the `TRIG` output, while `A4` handles `ECHO`.

---

## 6. Firmware Logic & Conversion Formula

When ready to integrate the JSN-SR04T, the surface water depth percentage is calculated from distance:

```cpp
// Physical Container Geometry Constants (in Centimeters)
const float SENSOR_CLEARANCE_CM = 25.0; // Distance from transducer to 100% full mark
const float CONTAINER_DEPTH_CM  = 16.0; // Total usable water depth (from empty to 100% full)

// Read raw distance in cm via ultrasonic echo
float measureDistanceCM() {
  digitalWrite(PIN_TRIG, LOW);
  delayMicroseconds(2);
  digitalWrite(PIN_TRIG, HIGH);
  delayMicroseconds(10);
  digitalWrite(PIN_TRIG, LOW);

  unsigned long duration = pulseIn(PIN_ECHO, HIGH, 30000UL); // 30ms timeout (~5m)
  if (duration == 0) return -1.0; // Timeout or obstruction
  
  return (float)duration * 0.0343 / 2.0;
}

// Convert measured distance into ponding water depth percentage (0.0% to 100.0%)
float calculateWaterPercentage(float distanceCM) {
  if (distanceCM < 0) return 0.0; // Error fallback

  // Distance when container is 100% full = SENSOR_CLEARANCE_CM (e.g. 25.0 cm)
  // Distance when container is 0% empty  = SENSOR_CLEARANCE_CM + CONTAINER_DEPTH_CM (e.g. 41.0 cm)
  float emptyDistance = SENSOR_CLEARANCE_CM + CONTAINER_DEPTH_CM;
  
  float currentWaterDepth = emptyDistance - distanceCM;
  float percentage = (currentWaterDepth / CONTAINER_DEPTH_CM) * 100.0;
  
  return constrain(percentage, 0.0, 100.0);
}
```

---

## 7. Immediate Takeaways & Next Steps

1. **Keep Existing Firmware Intact:** No production code or running sketches have been modified per your instruction.
2. **Current System Compatibility:** The existing 50% target and 45% refill decision thresholds and 10-second settling logic remain identical regardless of sensor type.
3. **Mounting Preparation:** When acquiring the JSN-SR04T, prepare a rigid overhead bracket (PVC arm, acrylic mount, or wooden dowel) allowing at least **25 cm** clearance above your target flood level.
