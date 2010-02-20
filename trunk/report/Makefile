
all: report.pdf manual.pdf

report.pdf: report.tex
	pdflatex report.tex
	pdflatex report.tex

manual.pdf: manual_comp.tex
	pdflatex --jobname manual manual_comp.tex
	pdflatex --jobname manual manual_comp.tex

clean:
	rm -f manual.pdf report.pdf *.aux *.gz *.toc *.log *.lof *.lol *.lot *.dvi *.idx *.loa
