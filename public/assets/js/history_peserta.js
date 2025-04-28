document.addEventListener("DOMContentLoaded", function () {
  // Theme toggling with smooth animation (unchanged)
  const themeToggle = document.getElementById("theme-toggle");
  const body = document.body;

  // Check for saved theme preference or use preferred color scheme
  const savedTheme = localStorage.getItem("preferredTheme");
  if (savedTheme === "dark") {
    body.classList.add("dark-mode");
  } else if (savedTheme === "light") {
    body.classList.remove("dark-mode");
  } else {
    // If no saved preference, check system preference
    if (
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
      body.classList.add("dark-mode");
    }
  }

  // Theme toggle button with animation
  themeToggle.addEventListener("click", function () {
    themeToggle.classList.add("theme-toggle-spin");

    setTimeout(() => {
      body.classList.toggle("dark-mode");

      // Save preference
      if (body.classList.contains("dark-mode")) {
        localStorage.setItem("preferredTheme", "dark");
      } else {
        localStorage.setItem("preferredTheme", "light");
      }
    }, 150);

    setTimeout(() => {
      themeToggle.classList.remove("theme-toggle-spin");
    }, 500);
  });

  // Premium PDF Generation Functionality
  const downloadPdfBtn = document.getElementById("download-pdf");
  if (downloadPdfBtn) {
    downloadPdfBtn.addEventListener("click", function () {
      // Add loading state
      const originalText = this.innerHTML;
      this.innerHTML =
        '<i class="bi bi-hourglass-split"></i> Menyiapkan PDF...';
      this.disabled = true;
      this.classList.add("btn-loading");

      // Small delay to show loading state before generating PDF
      setTimeout(() => {
        generateElegantPDF()
          .then(() => {
            // Show success state
            this.innerHTML = '<i class="bi bi-check-circle"></i> Berhasil';
            this.classList.remove("btn-loading");
            this.classList.add("btn-success");

            // Reset button after 2 seconds
            setTimeout(() => {
              this.innerHTML = originalText;
              this.disabled = false;
              this.classList.remove("btn-success");
            }, 2000);
          })
          .catch((error) => {
            console.error("Error generating PDF:", error);

            // Show error state
            this.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Gagal';
            this.classList.remove("btn-loading");
            this.classList.add("btn-error");

            // Reset button after 2 seconds
            setTimeout(() => {
              this.innerHTML = originalText;
              this.disabled = false;
              this.classList.remove("btn-error");
            }, 2000);
          });
      }, 300);
    });
  }

  // Fix for PDF generation - These functions need to be at the top level of the DOMContentLoaded event
  async function generateElegantPDF() {
    try {
      // Define color schemes for PDF
      const primaryColor = [48, 72, 120]; // #304878
      const secondaryColor = [82, 92, 140]; // #525C8C
      const accentColor = [235, 87, 87]; // #EB5757
      const lightGray = [245, 245, 245]; // #F5F5F5
      const textColor = [51, 51, 51]; // #333333
      const neutralColor = [102, 102, 102]; // #666666

      // Make sure jsPDF is available
      if (typeof window.jspdf === "undefined") {
        showNotification(
          "PDF generator tidak tersedia. Silakan coba lagi nanti.",
          "error"
        );
        return Promise.reject("jsPDF library not found");
      }

      const { jsPDF } = window.jspdf;
      const doc = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: "a4",
      });

      // Get institution name
      const institutionName = "Riwayat Ujian";

      // Get table data
      const table = document.getElementById("exam-history-table");
      if (!table) {
        showNotification("Tidak ada data ujian untuk diunduh", "warning");
        return Promise.reject("No table data found");
      }

      // Get actual username from page - try to find it, or use default
      const usernameElement = document.querySelector(
        ".user-name, .username, .profile-name"
      );
      const username = usernameElement
        ? usernameElement.textContent.trim()
        : document.title.includes("-")
        ? document.title.split("-")[1].trim()
        : "Peserta";

      // Sanitize username
      const sanitizedUsername = username
        .replace(/[^a-z0-9]/gi, "_")
        .toLowerCase();

      // Function to add subtle texture to elements
      function addSubtleTexture(doc, x, y, width, height, baseColor, opacity) {
        // Save current state
        doc.saveGraphicsState();

        // Set low opacity
        doc.setGState(new doc.GState({ opacity: opacity }));

        // Add texture by creating small dots with slight color variation
        const dotSize = 0.5;
        const spacing = 4;

        for (let i = 0; i < width; i += spacing) {
          for (let j = 0; j < height; j += spacing) {
            // Vary the color slightly
            const variation = Math.floor(Math.random() * 15);
            doc.setFillColor(
              Math.min(baseColor[0] + variation, 255),
              Math.min(baseColor[1] + variation, 255),
              Math.min(baseColor[2] + variation, 255)
            );

            // Random position variation
            const offsetX = Math.random() * 2;
            const offsetY = Math.random() * 2;

            doc.circle(x + i + offsetX, y + j + offsetY, dotSize, "F");
          }
        }

        // Restore original state
        doc.restoreGraphicsState();
      }

      // Function to draw elegant progress bar
      function drawProgressBar(
        doc,
        x,
        y,
        width,
        value,
        maxValue = 100,
        height = 6
      ) {
        // Background
        doc.setFillColor(240, 240, 240);
        doc.roundedRect(x, y, width, height, 2, 2, "F");

        // Progress
        const progressWidth = (value / maxValue) * width;

        // Determine color based on score
        let scoreColor;
        if (value >= 80) scoreColor = [39, 174, 96]; // Green for high scores
        else if (value >= 60)
          scoreColor = [52, 152, 219]; // Blue for medium scores
        else if (value >= 40)
          scoreColor = [243, 156, 18]; // Orange for low scores
        else scoreColor = [231, 76, 60]; // Red for very low scores

        doc.setFillColor(...scoreColor);

        // Only round both corners if 100%, otherwise just round left corners
        if (progressWidth >= width - 0.1) {
          doc.roundedRect(x, y, progressWidth, height, 2, 2, "F");
        } else if (progressWidth > 0) {
          // Custom rounded rectangle with only left corners rounded
          doc.setLineWidth(0);

          // Left side is rounded
          const r = 2; // Corner radius

          doc.lines(
            [
              [0, height - r], // Move to bottom-left corner minus radius
              [0, -(height - 2 * r)], // Line up
              [r, -r], // Arc to top-left corner
              [progressWidth - 2 * r, 0], // Line across top
              [r, r], // Arc to top-right corner
              [0, height - 2 * r], // Line down right side
              [-progressWidth, 0], // Line back to start
            ],
            x,
            y + r,
            [1, 1],
            "F"
          );
        }
      }

      // ------------------------
      // ELEGANT HEADER SECTION
      // ------------------------

      // Add elegant background with subtle gradient
      doc.setFillColor(255, 255, 255);
      doc.rect(
        0,
        0,
        doc.internal.pageSize.width,
        doc.internal.pageSize.height,
        "F"
      );

      // Add sophisticated header
      doc.setFillColor(...primaryColor);
      doc.rect(0, 0, doc.internal.pageSize.width, 40, "F");

      // Add subtle pattern to header
      addSubtleTexture(
        doc,
        0,
        0,
        doc.internal.pageSize.width,
        40,
        primaryColor,
        0.1
      );

      // Add accent line
      doc.setFillColor(...accentColor);
      doc.rect(0, 40, doc.internal.pageSize.width, 1.5, "F");

      // Add title text
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(24);
      doc.setFont("helvetica", "bold");
      doc.text(institutionName, 15, 20);

      // Add subtitle
      doc.setFontSize(12);
      doc.setTextColor(230, 230, 230);
      doc.setFont("helvetica", "normal");
      doc.text("Sistem Informasi Ujian Online", 15, 28);

      // Add decorative element
      doc.setDrawColor(...accentColor);
      doc.setLineWidth(0.5);
      doc.line(15, 32, 80, 32);

      // ------------------------
      // REPORT TITLE SECTION
      // ------------------------

      // Light background for document title area
      doc.setFillColor(...lightGray);
      doc.rect(0, 50, doc.internal.pageSize.width, 15, "F");

      // Add document title
      doc.setTextColor(...primaryColor);
      doc.setFontSize(16);
      doc.setFont("helvetica", "bold");
      doc.text("LAPORAN RIWAYAT UJIAN", doc.internal.pageSize.width / 2, 60, {
        align: "center",
      });

      // Add decorative elements
      doc.setDrawColor(...secondaryColor);
      doc.setLineWidth(0.5);
      const titleWidth =
        (doc.getStringUnitWidth("LAPORAN RIWAYAT UJIAN") * 16) /
        doc.internal.scaleFactor;
      const titleStart = (doc.internal.pageSize.width - titleWidth) / 2;
      doc.line(titleStart, 63, titleStart + titleWidth, 63);

      // ------------------------
      // USER INFO CARD
      // ------------------------

      // Background for user info with elegant border
      doc.setDrawColor(...accentColor);
      doc.setLineWidth(0.5);
      doc.setFillColor(250, 250, 250);
      doc.roundedRect(15, 75, doc.internal.pageSize.width - 30, 30, 3, 3, "FD");

      // Add date and user info
      doc.setFontSize(11);
      doc.setTextColor(...textColor);

      const dateOptions = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
      };
      const currentDate = new Date().toLocaleDateString("id-ID", dateOptions);

      // Left column
      doc.setFont("helvetica", "bold");
      doc.text(`Tanggal cetak:`, 25, 85);
      doc.setFont("helvetica", "normal");
      doc.text(currentDate, 70, 85);

      // Right column
      doc.setFont("helvetica", "bold");
      doc.text(`Nama Peserta:`, 25, 95);
      doc.setFont("helvetica", "normal");
      doc.text(username, 70, 95);

      // Add decorative corner element
      function drawCornerAccent(x, y, size, color) {
        doc.setDrawColor(...color);
        doc.setLineWidth(1);
        doc.line(x, y + size, x, y);
        doc.line(x, y, x + size, y);
      }

      // Draw corner accents
      drawCornerAccent(15, 75, 5, secondaryColor);
      drawCornerAccent(
        doc.internal.pageSize.width - 15,
        75,
        -5,
        secondaryColor
      );
      drawCornerAccent(15, 105, 5, secondaryColor);
      drawCornerAccent(
        doc.internal.pageSize.width - 15,
        105,
        -5,
        secondaryColor
      );

      // ------------------------
      // TABLE PREPARATION
      // ------------------------

      // Extract data for PDF table
      const tableData = [];
      const headers = [];

      // Get headers (without icons)
      const headerRow = table.querySelector("thead tr");
      headerRow.querySelectorAll("th").forEach((th) => {
        // Clean header text (remove icons)
        const headerText = th.textContent
          .trim()
          .replace(/^\s*[\r\n]+|[\r\n]+\s*$/g, "");
        headers.push(headerText);
      });

      // Get row data and prepare score data for visualization
      table.querySelectorAll("tbody tr").forEach((row) => {
        const rowData = [];

        row.querySelectorAll("td").forEach((cell) => {
          rowData.push(cell.textContent.trim());
        });

        tableData.push(rowData);
      });

      // ------------------------
      // ELEGANT DATA TABLE
      // ------------------------

      // Load autoTable plugin
      if (typeof doc.autoTable !== "function") {
        showNotification("AutoTable plugin tidak tersedia.", "error");
        return Promise.reject("AutoTable plugin not found");
      }

      // Generate the elegant table
      doc.autoTable({
        head: [headers],
        body: tableData,
        startY: 115,
        theme: "grid",
        styles: {
          fontSize: 10,
          cellPadding: 8,
          lineColor: [200, 200, 200],
          lineWidth: 0.1,
          font: "helvetica",
          textColor: [...textColor],
        },
        headStyles: {
          fillColor: [...secondaryColor],
          textColor: [255, 255, 255],
          fontStyle: "bold",
          halign: "center",
        },
        columnStyles: {
          0: { cellWidth: "auto" },
          1: { cellWidth: 25, halign: "center", fontStyle: "bold" },
          2: { cellWidth: "auto" },
        },
        alternateRowStyles: {
          fillColor: [248, 248, 248],
        },
        // Custom cell rendering for scores with elegant formatting
        didDrawCell: function (data) {
          if (
            data.section === "body" &&
            data.column.index === 1 &&
            data.cell.raw !== null
          ) {
            // Get score value
            const score = parseInt(data.cell.raw, 10);

            // Calculate position for score bar
            const barX = data.cell.x + 2;
            const barY = data.cell.y + data.cell.height - 3.5;
            const barWidth = data.cell.width - 4;

            // Draw progress bar under the score
            drawProgressBar(doc, barX, barY, barWidth, score, 100, 2);
          }
        },
      });

      // ------------------------
      // SUMMARY SECTION
      // ------------------------

      // Calculate statistics
      let totalScore = 0;
      let highestScore = 0;
      let lowestScore = 100;

      tableData.forEach((row) => {
        const score = parseInt(row[1], 10);
        totalScore += score;
        highestScore = Math.max(highestScore, score);
        lowestScore = Math.min(lowestScore, score);
      });

      const averageScore =
        tableData.length > 0 ? (totalScore / tableData.length).toFixed(1) : 0;

      // Get final Y position from the table and add spacing
      const finalY = doc.previousAutoTable.finalY + 25;

      // Add elegant summary section heading
      doc.setFillColor(...secondaryColor);
      doc.roundedRect(
        15,
        finalY - 10,
        doc.internal.pageSize.width - 30,
        10,
        2,
        2,
        "F"
      );

      doc.setTextColor(255, 255, 255);
      doc.setFontSize(12);
      doc.setFont("helvetica", "bold");
      doc.text(
        "Ringkasan Nilai",
        doc.internal.pageSize.width / 2,
        finalY - 2.5,
        {
          align: "center",
        }
      );

      // Background for summary content
      doc.setDrawColor(...accentColor);
      doc.setLineWidth(0.5);
      doc.setFillColor(250, 250, 250);
      doc.roundedRect(
        15,
        finalY,
        doc.internal.pageSize.width - 30,
        50,
        3,
        3,
        "FD"
      );

      // Create elegant summary boxes
      function createInfoBox(title, value, x, y, width, height) {
        // Box background and border
        doc.setFillColor(255, 255, 255);
        doc.setDrawColor(...accentColor);
        doc.setLineWidth(0.2);
        doc.roundedRect(x, y, width, height, 2, 2, "FD");

        // Box title
        doc.setTextColor(...secondaryColor);
        doc.setFontSize(9);
        doc.setFont("helvetica", "bold");
        doc.text(title, x + width / 2, y + 5, { align: "center" });

        // Separator line
        doc.setDrawColor(...lightGray);
        doc.setLineWidth(0.5);
        doc.line(x + 5, y + 7, x + width - 5, y + 7);

        // Box value
        doc.setTextColor(...textColor);
        doc.setFontSize(14);
        doc.setFont("helvetica", "bold");
        doc.text(String(value), x + width / 2, y + 17, { align: "center" });
      }

      // Draw summary boxes
      const boxWidth = (doc.internal.pageSize.width - 30 - 25) / 4;
      const boxHeight = 22;
      const boxY = finalY + 10;

      createInfoBox(
        "Total Ujian",
        tableData.length + " ujian",
        25,
        boxY,
        boxWidth,
        boxHeight
      );
      createInfoBox(
        "Nilai Rata-rata",
        averageScore,
        25 + boxWidth + 5,
        boxY,
        boxWidth,
        boxHeight
      );
      createInfoBox(
        "Nilai Tertinggi",
        highestScore,
        25 + (boxWidth + 5) * 2,
        boxY,
        boxWidth,
        boxHeight
      );
      createInfoBox(
        "Nilai Terendah",
        lowestScore,
        25 + (boxWidth + 5) * 3,
        boxY,
        boxWidth,
        boxHeight
      );

      // ------------------------
      // ELEGANT FOOTER
      // ------------------------

      function addElegantFooter(doc, currentPage, pageCount) {
        const pageHeight = doc.internal.pageSize.height;
        const pageWidth = doc.internal.pageSize.width;

        // Add footer background
        doc.setFillColor(...primaryColor);
        doc.rect(0, pageHeight - 20, pageWidth, 20, "F");

        // Add subtle pattern
        addSubtleTexture(
          doc,
          0,
          pageHeight - 20,
          pageWidth,
          20,
          primaryColor,
          0.05
        );

        // Add accent line
        doc.setFillColor(...accentColor);
        doc.rect(0, pageHeight - 20, pageWidth, 0.5, "F");

        // Add page numbers
        doc.setFontSize(9);
        doc.setTextColor(255, 255, 255);
        doc.text(
          `Halaman ${currentPage} dari ${pageCount}`,
          pageWidth / 2,
          pageHeight - 8,
          {
            align: "center",
          }
        );

        // Add copyright
        const year = new Date().getFullYear();
        doc.setFontSize(8);
        doc.text(`© ${year} Sistem Informasi Ujian Online`, 15, pageHeight - 8);

        // Add generation note
        doc.text(
          "Dokumen ini dibuat secara otomatis",
          pageWidth - 15,
          pageHeight - 8,
          { align: "right" }
        );
      }

      // Add elegant footer with page numbers
      const pageCount = doc.internal.getNumberOfPages();
      for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        addElegantFooter(doc, i, pageCount);
      }

      // ------------------------
      // WATERMARK
      // ------------------------

      // Add subtle watermark to each page
      for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);

        // Add generation timestamp in tiny text
        const timestamp = new Date().toISOString();
        doc.setFontSize(6);
        doc.setTextColor(200, 200, 200);
        doc.text(timestamp, doc.internal.pageSize.width - 15, 5, {
          align: "right",
        });
      }

      // ------------------------
      // SAVE PDF
      // ------------------------

      // Save the PDF with dynamic name
      const dateStr = new Date().toISOString().slice(0, 10);
      const pdfFileName = `Riwayat_Ujian_${sanitizedUsername}_${dateStr}.pdf`;
      doc.save(pdfFileName);

      // Show success notification
      showNotification("PDF berhasil diunduh!", "success");
      return Promise.resolve();
    } catch (error) {
      console.error("Error generating PDF:", error);
      showNotification(
        "Terjadi kesalahan saat membuat PDF. " + error.message,
        "error"
      );
      return Promise.reject(error);
    }
    // ------------------------
    // ELEGANT HEADER SECTION
    // ------------------------

    // Add elegant background with subtle gradient
    doc.setFillColor(255, 255, 255);
    doc.rect(
      0,
      0,
      doc.internal.pageSize.width,
      doc.internal.pageSize.height,
      "F"
    );

    // Add sophisticated header
    doc.setFillColor(...primaryColor);
    doc.rect(0, 0, doc.internal.pageSize.width, 40, "F");

    // Add subtle pattern to header
    addSubtleTexture(
      doc,
      0,
      0,
      doc.internal.pageSize.width,
      40,
      primaryColor,
      0.1
    );

    // Add accent line
    doc.setFillColor(...accentColor);
    doc.rect(0, 40, doc.internal.pageSize.width, 1.5, "F");

    // Add title text
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(24);
    doc.setFont("helvetica", "bold");
    doc.text(institutionName, 15, 20);

    // Add subtitle
    doc.setFontSize(12);
    doc.setTextColor(230, 230, 230);
    doc.setFont("helvetica", "normal");
    doc.text("Sistem Informasi Ujian Online", 15, 28);

    // Add decorative element
    doc.setDrawColor(...accentColor);
    doc.setLineWidth(0.5);
    doc.line(15, 32, 80, 32);

    // ------------------------
    // REPORT TITLE SECTION
    // ------------------------

    // Light background for document title area
    doc.setFillColor(...lightGray);
    doc.rect(0, 50, doc.internal.pageSize.width, 15, "F");

    // Add document title
    doc.setTextColor(...primaryColor);
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("LAPORAN RIWAYAT UJIAN", doc.internal.pageSize.width / 2, 60, {
      align: "center",
    });

    // Add decorative elements
    doc.setDrawColor(...secondaryColor);
    doc.setLineWidth(0.5);
    const titleWidth =
      (doc.getStringUnitWidth("LAPORAN RIWAYAT UJIAN") * 16) /
      doc.internal.scaleFactor;
    const titleStart = (doc.internal.pageSize.width - titleWidth) / 2;
    doc.line(titleStart, 63, titleStart + titleWidth, 63);

    // ------------------------
    // USER INFO CARD
    // ------------------------

    // Background for user info with elegant border
    doc.setDrawColor(...accentColor);
    doc.setLineWidth(0.5);
    doc.setFillColor(250, 250, 250);
    doc.roundedRect(15, 75, doc.internal.pageSize.width - 30, 30, 3, 3, "FD");

    // Add date and user info
    doc.setFontSize(11);
    doc.setTextColor(...textColor);

    const dateOptions = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    };
    const currentDate = new Date().toLocaleDateString("id-ID", dateOptions);

    // Left column
    doc.setFont("helvetica", "bold");
    doc.text(`Tanggal cetak:`, 25, 85);
    doc.setFont("helvetica", "normal");
    doc.text(currentDate, 70, 85);

    // Right column
    doc.setFont("helvetica", "bold");
    doc.text(`Nama Peserta:`, 25, 95);
    doc.setFont("helvetica", "normal");
    doc.text(username, 70, 95);

    // Add decorative corner element
    function drawCornerAccent(x, y, size, color) {
      doc.setDrawColor(...color);
      doc.setLineWidth(1);
      doc.line(x, y + size, x, y);
      doc.line(x, y, x + size, y);
    }

    // Draw corner accents
    drawCornerAccent(15, 75, 5, secondaryColor);
    drawCornerAccent(doc.internal.pageSize.width - 15, 75, -5, secondaryColor);
    drawCornerAccent(15, 105, 5, secondaryColor);
    drawCornerAccent(doc.internal.pageSize.width - 15, 105, -5, secondaryColor);

    // ------------------------
    // TABLE PREPARATION
    // ------------------------

    // Extract data for PDF table
    const tableData = [];
    const headers = [];

    // Get headers (without icons)
    const headerRow = table.querySelector("thead tr");
    headerRow.querySelectorAll("th").forEach((th) => {
      // Clean header text (remove icons)
      const headerText = th.textContent
        .trim()
        .replace(/^\s*[\r\n]+|[\r\n]+\s*$/g, "");
      headers.push(headerText);
    });

    // Get row data and prepare score data for visualization
    table.querySelectorAll("tbody tr").forEach((row) => {
      const rowData = [];

      row.querySelectorAll("td").forEach((cell, index) => {
        rowData.push(cell.textContent.trim());
      });

      tableData.push(rowData);
    });

    // ------------------------
    // ELEGANT DATA TABLE
    // ------------------------

    // Generate the elegant table
    doc.autoTable({
      head: [headers],
      body: tableData,
      startY: 115,
      theme: "grid",
      styles: {
        fontSize: 10,
        cellPadding: 8,
        lineColor: [200, 200, 200],
        lineWidth: 0.1,
        font: "helvetica",
        textColor: [...textColor],
      },
      headStyles: {
        fillColor: [...secondaryColor],
        textColor: [255, 255, 255],
        fontStyle: "bold",
        halign: "center",
      },
      columnStyles: {
        0: { cellWidth: "auto" },
        1: { cellWidth: 25, halign: "center", fontStyle: "bold" },
        2: { cellWidth: "auto" },
      },
      alternateRowStyles: {
        fillColor: [248, 248, 248],
      },
      // Custom cell rendering for scores with elegant formatting
      didDrawCell: function (data) {
        if (
          data.section === "body" &&
          data.column.index === 1 &&
          data.cell.raw !== null
        ) {
          // Get score value
          const score = parseInt(data.cell.raw, 10);

          // Calculate position for score bar
          const barX = data.cell.x + 2;
          const barY = data.cell.y + data.cell.height - 3.5;
          const barWidth = data.cell.width - 4;

          // Draw progress bar under the score
          drawProgressBar(doc, barX, barY, barWidth, score, 100, 2);
        }
      },
      // Add custom footer for the table
      didDrawPage: function (data) {
        doc.setDrawColor(...accentColor);
        doc.setLineWidth(0.5);
        doc.line(
          data.settings.margin.left,
          data.cursor.y + 5,
          doc.internal.pageSize.width - data.settings.margin.right,
          data.cursor.y + 5
        );
      },
    });

    // ------------------------
    // SUMMARY SECTION
    // ------------------------

    // Calculate statistics
    let totalScore = 0;
    let highestScore = 0;
    let lowestScore = 100;

    tableData.forEach((row) => {
      const score = parseInt(row[1], 10);
      totalScore += score;
      highestScore = Math.max(highestScore, score);
      lowestScore = Math.min(lowestScore, score);
    });

    const averageScore =
      tableData.length > 0 ? (totalScore / tableData.length).toFixed(1) : 0;

    // Get final Y position from the table and add spacing
    const finalY = doc.previousAutoTable.finalY + 25;

    // Add elegant summary section heading
    doc.setFillColor(...secondaryColor);
    doc.roundedRect(
      15,
      finalY - 10,
      doc.internal.pageSize.width - 30,
      10,
      2,
      2,
      "F"
    );

    doc.setTextColor(255, 255, 255);
    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    doc.text("Ringkasan Nilai", doc.internal.pageSize.width / 2, finalY - 2.5, {
      align: "center",
    });

    // Background for summary content
    doc.setDrawColor(...accentColor);
    doc.setLineWidth(0.5);
    doc.setFillColor(250, 250, 250);
    doc.roundedRect(
      15,
      finalY,
      doc.internal.pageSize.width - 30,
      50,
      3,
      3,
      "FD"
    );

    // Create elegant summary boxes
    function createInfoBox(title, value, x, y, width, height) {
      // Box background and border
      doc.setFillColor(255, 255, 255);
      doc.setDrawColor(...accentColor);
      doc.setLineWidth(0.2);
      doc.roundedRect(x, y, width, height, 2, 2, "FD");

      // Box title
      doc.setTextColor(...secondaryColor);
      doc.setFontSize(9);
      doc.setFont("helvetica", "bold");
      doc.text(title, x + width / 2, y + 5, { align: "center" });

      // Separator line
      doc.setDrawColor(...lightGray);
      doc.setLineWidth(0.5);
      doc.line(x + 5, y + 7, x + width - 5, y + 7);

      // Box value
      doc.setTextColor(...textColor);
      doc.setFontSize(14);
      doc.setFont("helvetica", "bold");
      doc.text(String(value), x + width / 2, y + 17, { align: "center" });
    }

    // Draw summary boxes
    const boxWidth = (doc.internal.pageSize.width - 30 - 25) / 4;
    const boxHeight = 22;
    const boxY = finalY + 10;

    createInfoBox(
      "Total Ujian",
      tableData.length + " ujian",
      25,
      boxY,
      boxWidth,
      boxHeight
    );
    createInfoBox(
      "Nilai Rata-rata",
      averageScore,
      25 + boxWidth + 5,
      boxY,
      boxWidth,
      boxHeight
    );
    createInfoBox(
      "Nilai Tertinggi",
      highestScore,
      25 + (boxWidth + 5) * 2,
      boxY,
      boxWidth,
      boxHeight
    );
    createInfoBox(
      "Nilai Terendah",
      lowestScore,
      25 + (boxWidth + 5) * 3,
      boxY,
      boxWidth,
      boxHeight
    );

    // Add elegant score chart if more than one exam
    if (tableData.length > 1) {
      const chartY = boxY + boxHeight + 8;

      // Add chart title
      doc.setTextColor(...secondaryColor);
      doc.setFontSize(9);
      doc.setFont("helvetica", "italic");
      doc.text(
        "Perbandingan Nilai Ujian",
        doc.internal.pageSize.width / 2,
        chartY,
        { align: "center" }
      );

      // Chart background
      doc.setFillColor(255, 255, 255);
      doc.setDrawColor(...accentColor);
      doc.setLineWidth(0.2);
      doc.roundedRect(
        25,
        chartY + 3,
        doc.internal.pageSize.width - 50,
        15,
        2,
        2,
        "FD"
      );

      // Draw elegant bars for each exam score
      const barSpacing = (doc.internal.pageSize.width - 60) / tableData.length;
      const maxBarWidth = barSpacing - 4;

      tableData.forEach((row, idx) => {
        const score = parseInt(row[1], 10);
        const barX = 30 + idx * barSpacing;
        const barY = chartY + 14;

        // Draw score bar
        drawProgressBar(doc, barX, barY, maxBarWidth, score, 100, 3);

        // Add exam name below (shortened)
        doc.setFontSize(7);
        doc.setTextColor(...neutralColor);

        // Truncate exam name properly
        let examName = row[0];
        if (examName.length > 12) {
          examName = examName.substring(0, 10) + "...";
        }

        doc.text(examName, barX + maxBarWidth / 2, barY + 6, {
          align: "center",
        });
      });
    }

    // ------------------------
    // ELEGANT FOOTER
    // ------------------------

    function addElegantFooter(doc, currentPage, pageCount) {
      doc.setPage(currentPage);

      const pageHeight = doc.internal.pageSize.height;
      const pageWidth = doc.internal.pageSize.width;

      // Add subtle gradient footer
      doc.setFillColor(...primaryColor);
      doc.rect(0, pageHeight - 20, pageWidth, 20, "F");

      // Add subtle pattern
      addSubtleTexture(
        doc,
        0,
        pageHeight - 20,
        pageWidth,
        20,
        primaryColor,
        0.05
      );

      // Add accent line
      doc.setFillColor(...accentColor);
      doc.rect(0, pageHeight - 20, pageWidth, 0.5, "F");

      // Add page numbers
      doc.setFontSize(9);
      doc.setTextColor(255, 255, 255);
      doc.text(
        `Halaman ${currentPage} dari ${pageCount}`,
        pageWidth / 2,
        pageHeight - 8,
        {
          align: "center",
        }
      );

      // Add copyright
      const year = new Date().getFullYear();
      doc.setFontSize(8);
      doc.text(`© ${year} Sistem Informasi Ujian Online`, 15, pageHeight - 8);

      // Add generation note
      doc.text(
        "Dokumen ini dibuat secara otomatis",
        pageWidth - 15,
        pageHeight - 8,
        { align: "right" }
      );

      // Add decorative element
      doc.setDrawColor(255, 255, 255);
      doc.setLineWidth(0.3);
      doc.line(
        pageWidth / 2 - 30,
        pageHeight - 12,
        pageWidth / 2 + 30,
        pageHeight - 12
      );
    }

    // Add elegant footer with page numbers
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
      addElegantFooter(doc, i, pageCount);
    }

    // ------------------------
    // WATERMARK AND SECURITY FEATURES
    // ------------------------

    // Add subtle watermark to each page
    for (let i = 1; i <= pageCount; i++) {
      doc.setPage(i);

      // Add subtle watermark
      doc.setTextColor(240, 240, 240);
      doc.setFontSize(60);
      doc.setFont("helvetica", "italic");
      doc.text(
        "UJIAN ONLINE",
        doc.internal.pageSize.width / 2,
        doc.internal.pageSize.height / 2,
        {
          align: "center",
          angle: 45,
        }
      );

      // Add generation timestamp in tiny text
      const timestamp = new Date().toISOString();
      doc.setFontSize(6);
      doc.setTextColor(200, 200, 200);
      doc.text(timestamp, doc.internal.pageSize.width - 15, 5, {
        align: "right",
      });
    }

    // ------------------------
    // SAVE PDF
    // ------------------------

    // Save the PDF with dynamic name using actual username
    const dateStr = new Date().toISOString().slice(0, 10);
    const sanitizedUsername = username
      .replace(/[^a-z0-9]/gi, "_")
      .toLowerCase();
    const pdfFileName = `Riwayat_Ujian_${sanitizedUsername}_${dateStr}.pdf`;
    doc.save(pdfFileName);

    // Show success notification
    showNotification("PDF berhasil diunduh!", "success");
  }

  // Show notification function
  function showNotification(message, type) {
    // Check if notification container exists, if not create it
    let notifContainer = document.querySelector(".notification-container");
    if (!notifContainer) {
      notifContainer = document.createElement("div");
      notifContainer.className = "notification-container";
      document.body.appendChild(notifContainer);

      // Add styles for notification container
      notifContainer.style.position = "fixed";
      notifContainer.style.top = "20px";
      notifContainer.style.right = "20px";
      notifContainer.style.zIndex = "9999";
    }

    // Create notification element
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;

    // Set icon based on type
    let icon = "info-circle";
    if (type === "success") icon = "check-circle";
    if (type === "error") icon = "exclamation-circle";
    if (type === "warning") icon = "exclamation-triangle";

    // Set notification content
    notification.innerHTML = `
            <div class="notification-content">
                <i class="bi bi-${icon}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="bi bi-x"></i>
            </button>
        `;

    // Style the notification with an elegant design
    notification.style.backgroundColor =
      type === "success"
        ? "#f0f9f1"
        : type === "error"
        ? "#fdf0f0"
        : type === "warning"
        ? "#fef9e7"
        : "#eef6fc";
    notification.style.color =
      type === "success"
        ? "#2c7a3d"
        : type === "error"
        ? "#b81c1c"
        : type === "warning"
        ? "#966a18"
        : "#245685";
    notification.style.borderLeft = `4px solid ${
      type === "success"
        ? "#4caf50"
        : type === "error"
        ? "#e74c3c"
        : type === "warning"
        ? "#f39c12"
        : "#3498db"
    }`;
    notification.style.padding = "14px 16px";
    notification.style.borderRadius = "4px";
    notification.style.marginBottom = "12px";
    notification.style.boxShadow = "0 3px 10px rgba(0, 0, 0, 0.08)";
    notification.style.display = "flex";
    notification.style.justifyContent = "space-between";
    notification.style.alignItems = "center";
    notification.style.minWidth = "320px";
    notification.style.maxWidth = "400px";
    notification.style.animation = "slide-in 0.3s ease-out forwards";
    notification.style.backdropFilter = "blur(5px)";

    // Style notification content
    const content = notification.querySelector(".notification-content");
    content.style.display = "flex";
    content.style.alignItems = "center";
    content.style.gap = "12px";

    // Style close button
    const closeBtn = notification.querySelector(".notification-close");
    closeBtn.style.background = "none";
    closeBtn.style.border = "none";
    closeBtn.style.color = "inherit";
    closeBtn.style.cursor = "pointer";
    closeBtn.style.fontSize = "16px";
    closeBtn.style.padding = "0";
    closeBtn.style.opacity = "0.6";
    closeBtn.style.transition = "opacity 0.2s";

    // Hover effect for close button
    closeBtn.addEventListener("mouseover", () => {
      closeBtn.style.opacity = "1";
    });
    closeBtn.addEventListener("mouseout", () => {
      closeBtn.style.opacity = "0.6";
    });

    // Add notification to container
    notifContainer.appendChild(notification);

    // Close notification on click
    closeBtn.addEventListener("click", () => {
      notification.style.animation = "slide-out 0.3s ease-out forwards";
      setTimeout(() => {
        notification.remove();
      }, 300);
    });

    // Auto close after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.style.animation = "slide-out 0.3s ease-out forwards";
        setTimeout(() => {
          notification.remove();
        }, 300);
      }
    }, 5000);
  }

  // Add animation keyframes and additional styles
  const style = document.createElement("style");
  style.innerHTML = `
        @keyframes slide-in {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slide-out {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes reveal {
            0% { width: 0; }
            100% { width: 100%; }
        }
        
        .theme-toggle-spin {
            animation: spin 0.5s ease-out;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.8;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .btn-loading i {
            animation: pulse 1.5s infinite ease-in-out;
        }
        
        .btn-success {
            background-color: #4caf50 !important;
            color: white !important;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .btn-error {
            background-color: #e74c3c !important;
            color: white !important;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .notification {
            transition: transform 0.3s, opacity 0.3s;
            backdrop-filter: blur(5px);
        }
        
        .notification-close:hover {
            transform: scale(1.15);
        }
        
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
        
        /* Elegant progress bars */
        .progress-bar {
            background-color: #f5f5f5;
            border-radius: 4px;
            height: 6px;
            margin: 8px 0;
            overflow: hidden;
            position: relative;
        }
        
        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
            background-image: linear-gradient(to right, #4e73df, #224abe);
        }
        
        /* PDF Button styling */
        .pdf-download-btn {
            background-color: #304878;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .pdf-download-btn:hover {
            background-color: #3a5a96;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .pdf-download-btn:active {
            transform: translateY(1px);
        }
        
        /* Table enhancements for mobile view */
        @media (max-width: 768px) {
            .custom-table {
                border: 0;
                box-shadow: none;
            }
            
            .custom-table thead {
                display: none;
            }
            
            .custom-table tbody tr {
                display: block;
                margin-bottom: 16px;
                border-radius: 6px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                background-color: white;
            }
            
            .custom-table td {
                display: flex;
                justify-content: space-between;
                text-align: right;
                padding: 12px 16px;
                border-bottom: 1px solid #f0f0f0;
            }
            
            .custom-table td:last-child {
                border-bottom: 0;
            }
            
            .custom-table td::before {
                content: attr(data-label);
                font-weight: bold;
                text-align: left;
                color: #555;
            }
        }
    `;
  document.head.appendChild(style);

  // Enhance the PDF download button appearance
  const enhancePdfButton = () => {
    const downloadPdfBtn = document.getElementById("download-pdf");
    if (downloadPdfBtn) {
      downloadPdfBtn.classList.add("pdf-download-btn");
      if (!downloadPdfBtn.querySelector("i")) {
        downloadPdfBtn.innerHTML = `<i class="bi bi-file-earmark-pdf"></i> ${downloadPdfBtn.textContent}`;
      }
    }
  };
  enhancePdfButton();

  // Add data-label attributes for mobile responsive view
  const tableRows = document.querySelectorAll(".custom-table tbody tr");
  tableRows.forEach((row) => {
    const cells = row.querySelectorAll("td");
    if (cells.length >= 3) {
      cells[0].setAttribute("data-label", "Judul Ujian");
      cells[1].setAttribute("data-label", "Nilai");
      cells[2].setAttribute("data-label", "Tanggal Pengerjaan");
    }
  });

  // Add enhanced score visualization to the table on the page
  const enhanceScoreDisplay = () => {
    const scoresCells = document.querySelectorAll(
      ".custom-table td:nth-child(2)"
    );

    scoresCells.forEach((cell) => {
      const score = parseInt(cell.textContent.trim(), 10);
      if (!isNaN(score)) {
        // Save the original text
        const originalText = cell.textContent.trim();

        // Create elegant score display
        const scoreDisplay = document.createElement("div");
        scoreDisplay.style.display = "flex";
        scoreDisplay.style.flexDirection = "column";
        scoreDisplay.style.alignItems = "center";
        scoreDisplay.style.gap = "5px";

        // Score value display
        const scoreValue = document.createElement("div");
        scoreValue.textContent = originalText;
        scoreValue.style.fontWeight = "bold";
        scoreValue.style.fontSize = "14px";

        // Progress bar container
        const progressBar = document.createElement("div");
        progressBar.className = "progress-bar";
        progressBar.style.width = "100%";

        // Progress bar fill
        const progressFill = document.createElement("div");
        progressFill.className = "progress-bar-fill";
        progressFill.style.width = `${score}%`;

        // Set color based on score
        if (score >= 80) {
          progressFill.style.backgroundImage =
            "linear-gradient(to right, #4caf50, #2e7d32)";
        } else if (score >= 60) {
          progressFill.style.backgroundImage =
            "linear-gradient(to right, #3498db, #2980b9)";
        } else if (score >= 40) {
          progressFill.style.backgroundImage =
            "linear-gradient(to right, #f39c12, #d35400)";
        } else {
          progressFill.style.backgroundImage =
            "linear-gradient(to right, #e74c3c, #c0392b)";
        }

        // Assemble the components
        progressBar.appendChild(progressFill);
        scoreDisplay.appendChild(scoreValue);
        scoreDisplay.appendChild(progressBar);

        // Replace the cell content
        cell.innerHTML = "";
        cell.appendChild(scoreDisplay);
      }
    });
  };

  // Apply enhanced score display if there's a table
  const examTable = document.getElementById("exam-history-table");
  if (examTable) {
    enhanceScoreDisplay();
  }

  // Add digital signature information to PDF
  function addDigitalSignature(doc, pageWidth, pageHeight) {
    // Create signature area
    const sigX = pageWidth - 90;
    const sigY = pageHeight - 60;
    const sigWidth = 70;
    const sigHeight = 30;

    // Add signature box with subtle styling
    doc.setDrawColor(200, 200, 200);
    doc.setLineWidth(0.1);
    doc.setFillColor(252, 252, 252);
    doc.roundedRect(sigX, sigY, sigWidth, sigHeight, 2, 2, "FD");

    // Add signature title
    doc.setFontSize(7);
    doc.setTextColor(100, 100, 100);
    doc.setFont("helvetica", "italic");
    doc.text("Ditandatangani secara digital", sigX + sigWidth / 2, sigY + 5, {
      align: "center",
    });

    // Add signature line
    doc.setDrawColor(180, 180, 180);
    doc.setLineWidth(0.2);
    doc.line(sigX + 5, sigY + 20, sigX + sigWidth - 5, sigY + 20);

    // Add signature verification text
    doc.setFontSize(6);
    doc.setTextColor(120, 120, 120);
    doc.text(
      "Dokumen ini memiliki tanda tangan digital",
      sigX + sigWidth / 2,
      sigY + 25,
      { align: "center" }
    );

    // Add a secure stamp icon (simple drawing)
    doc.setDrawColor(100, 100, 100);
    doc.setFillColor(230, 230, 230);
    doc.circle(sigX + 10, sigY + 10, 4, "FD");
    doc.setLineWidth(0.5);
    doc.line(sigX + 8, sigY + 10, sigX + 12, sigY + 10);
    doc.line(sigX + 10, sigY + 8, sigX + 10, sigY + 12);
  }

  // Add elegant summary chart for visual appeal
  function drawSummaryChart(doc, x, y, width, height, data) {
    // Background and border
    doc.setDrawColor(220, 220, 220);
    doc.setFillColor(250, 250, 250);
    doc.setLineWidth(0.2);
    doc.roundedRect(x, y, width, height, 3, 3, "FD");

    // Chart title
    doc.setTextColor(80, 80, 80);
    doc.setFontSize(9);
    doc.setFont("helvetica", "bold");
    doc.text("Distribusi Nilai", x + width / 2, y + 7, { align: "center" });

    // Draw axis
    doc.setDrawColor(200, 200, 200);
    doc.setLineWidth(0.3);
    doc.line(x + 20, y + height - 10, x + width - 20, y + height - 10); // X axis
    doc.line(x + 20, y + 15, x + 20, y + height - 10); // Y axis

    // Calculate chart dimensions
    const chartAreaX = x + 25;
    const chartAreaY = y + 15;
    const chartAreaWidth = width - 45;
    const chartAreaHeight = height - 25;

    // Get min and max values
    const values = data.map((item) => item.value);
    const maxValue = Math.max(...values);

    // Draw bars
    const barWidth = chartAreaWidth / data.length;
    const spacing = 2;

    data.forEach((item, index) => {
      const barHeight = (item.value / 100) * chartAreaHeight;
      const barX = chartAreaX + index * barWidth + spacing / 2;
      const barY = chartAreaY + chartAreaHeight - barHeight;

      // Gradient fill for bar
      const colorStep = index / data.length;
      const r = Math.floor(100 + colorStep * 50);
      const g = Math.floor(120 + colorStep * 50);
      const b = Math.floor(180 - colorStep * 50);

      doc.setFillColor(r, g, b);
      doc.rect(barX, barY, barWidth - spacing, barHeight, "F");

      // Add value on top
      doc.setTextColor(50, 50, 50);
      doc.setFontSize(6);
      doc.text(String(item.value), barX + (barWidth - spacing) / 2, barY - 2, {
        align: "center",
      });

      // Add label below
      doc.setTextColor(100, 100, 100);
      doc.setFontSize(6);
      doc.text(item.label, barX + (barWidth - spacing) / 2, y + height - 5, {
        align: "center",
      });
    });
  }

  // Add event listeners for hover effects on the table
  function enhanceTableInteractivity() {
    const tableRows = document.querySelectorAll("#exam-history-table tbody tr");

    tableRows.forEach((row) => {
      // Add hover effect
      row.addEventListener("mouseenter", () => {
        row.style.transition = "background-color 0.3s";
        row.style.backgroundColor = "rgba(240, 245, 255, 0.7)";
        row.style.cursor = "pointer";
      });

      row.addEventListener("mouseleave", () => {
        row.style.backgroundColor = "";
      });

      // Add click effect to show detailed info
      row.addEventListener("click", () => {
        // Get exam data
        const examTitle = row
          .querySelector("td:first-child")
          .textContent.trim();
        const examScore = row
          .querySelector("td:nth-child(2)")
          .textContent.trim();
        const examDate = row
          .querySelector("td:nth-child(3)")
          .textContent.trim();

        // Show detailed info in a modal or notification
        showExamDetailModal(examTitle, examScore, examDate);
      });
    });
  }

  // Create a modal to show exam details
  function showExamDetailModal(title, score, date) {
    // Create modal container
    const modal = document.createElement("div");
    modal.className = "exam-detail-modal";
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    modal.style.display = "flex";
    modal.style.justifyContent = "center";
    modal.style.alignItems = "center";
    modal.style.zIndex = "9999";
    modal.style.opacity = "0";
    modal.style.transition = "opacity 0.3s";

    // Create modal content
    const modalContent = document.createElement("div");
    modalContent.className = "exam-detail-content";
    modalContent.style.backgroundColor = "white";
    modalContent.style.borderRadius = "8px";
    modalContent.style.padding = "20px";
    modalContent.style.width = "90%";
    modalContent.style.maxWidth = "500px";
    modalContent.style.boxShadow = "0 10px 25px rgba(0, 0, 0, 0.15)";
    modalContent.style.transform = "translateY(20px)";
    modalContent.style.transition = "transform 0.3s";

    // Format score for display
    const scoreValue = parseInt(score, 10);
    let scoreStatus = "";
    let scoreColor = "";

    if (scoreValue >= 80) {
      scoreStatus = "Sangat Baik";
      scoreColor = "#4caf50";
    } else if (scoreValue >= 70) {
      scoreStatus = "Baik";
      scoreColor = "#3498db";
    } else if (scoreValue >= 60) {
      scoreStatus = "Cukup";
      scoreColor = "#f39c12";
    } else {
      scoreStatus = "Perlu Perbaikan";
      scoreColor = "#e74c3c";
    }

    // Add content to modal
    modalContent.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #304878; font-size: 18px;">Detail Ujian</h3>
                <button class="modal-close" style="background: none; border: none; font-size: 20px; cursor: pointer;">×</button>
            </div>
            <div style="padding: 15px; background-color: #f8f9fa; border-radius: 6px; margin-bottom: 15px;">
                <h4 style="margin: 0 0 10px 0; color: #333; font-size: 16px;">${title}</h4>
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: bold; margin-right: 10px;">Nilai:</span>
                    <span style="background-color: ${scoreColor}; color: white; padding: 4px 10px; border-radius: 20px; font-weight: bold;">${score}</span>
                    <span style="margin-left: 10px; color: #666;">(${scoreStatus})</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <span style="font-weight: bold; margin-right: 10px;">Tanggal:</span>
                    <span>${date}</span>
                </div>
            </div>
            <div style="text-align: center;">
                <button class="primary-btn" style="background-color: #304878; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 10px;">
                    Lihat Detail Soal
                </button>
                <button class="secondary-btn" style="background-color: transparent; color: #304878; border: 1px solid #304878; padding: 8px 15px; border-radius: 4px; cursor: pointer;">
                    Tutup
                </button>
            </div>
        `;

    // Append modal to body
    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    // Show modal with animation
    setTimeout(() => {
      modal.style.opacity = "1";
      modalContent.style.transform = "translateY(0)";
    }, 10);

    // Add event listeners for closing modal
    const closeModal = () => {
      modal.style.opacity = "0";
      modalContent.style.transform = "translateY(20px)";
      setTimeout(() => {
        document.body.removeChild(modal);
      }, 300);
    };

    modal.querySelector(".modal-close").addEventListener("click", closeModal);
    modal.querySelector(".secondary-btn").addEventListener("click", closeModal);
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        closeModal();
      }
    });

    // Prevent default action for primary button
    modal.querySelector(".primary-btn").addEventListener("click", (e) => {
      e.preventDefault();
      // This would normally link to exam details page
      showNotification(`Detail soal untuk ${title} akan ditampilkan`, "info");
      closeModal();
    });
  }

  // Initialize table interactivity if table exists
  if (examTable) {
    enhanceTableInteractivity();
  }

  // Helper function to check if an element is in viewport
  function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <=
        (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }

  // Add scroll animations for table rows
  function addScrollAnimations() {
    const tableRows = document.querySelectorAll("#exam-history-table tbody tr");

    // Function to check if elements are visible and add animation class
    function checkVisibility() {
      tableRows.forEach((row, index) => {
        if (isElementInViewport(row) && !row.classList.contains("animated")) {
          // Add animation with delay based on index
          setTimeout(() => {
            row.style.animation = "fade-in 0.5s ease forwards";
            row.classList.add("animated");
          }, index * 100);
        }
      });
    }

    // Initial check
    checkVisibility();

    // Check on scroll
    window.addEventListener("scroll", checkVisibility);
  }

  // Initialize animations if table exists
  if (examTable) {
    addScrollAnimations();
  }
});
